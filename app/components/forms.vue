<template>

    <div>

        <div v-if="forms.length">

            <partial name="toolbar"></partial>

            <div class="uk-margin-top uk-margin-small-bottom" v-if="forms.length > 1">
                <div class="uk-search">
                    <input class="uk-search-field uk-form-width-large" type="text" v-model="search" debounce="300">
                </div>
            </div>

            <nav class="uk-subnav uk-subnav-pill" v-if="categoryNav.length">
                <li class="uk-disabled">
                    <a>Categories</a>
                </li>
                <li v-for="item in categoryNav" :class="isActive('categories', item) ? 'uk-active' : ''" :key="item">
                    <a @click="click('categories', item)">{{ item }}</a>
                </li>
            </nav>

            <nav class="uk-subnav uk-subnav-pill uk-margin-bottom uk-margin-small-top">
                <li class="uk-disabled">
                    <a>Selected</a>
                </li>
                <li v-for="item in selectNav" :class="isActive('selected', item.name) ? 'uk-active' : ''" :key="item.name">
                    <a @click="click('selected', item.name)">{{ item.label }}</a>
                </li>
            </nav>

            <div class="uk-panel uk-panel-divider" v-for="form in active" :key="form.name">
                <form class="uk-form-horizontal">
                    <h1>{{ form.label }}</h1>
                    <p v-if="form.help">{{ form.help }}</p>
                    <fieldset v-for="fieldset in form.fieldsets" :key="fieldset.name" style="margin: 15px 0;">
                        <legend>
                            {{ fieldset.label }}
                            <label v-if="doInherit(fieldset)">
                                 | <input type="checkbox" v-model="values[form.name][fieldset.name].inherit.enabled">
                                <small>Inherit from Settings > {{ fieldset.inherit.label }}</small>
                            </label>
                        </legend>
                        <p v-if="fieldset.help">{{ fieldset.help }}</p>
                        <fields v-if="!isInherited(form.name, fieldset.name)" :config="fieldset.fields" :values="values[form.name][fieldset.name]"></fields>
                    </fieldset>
                </form>
            </div>

            <h3 v-if="!active.length" class="uk-h1 uk-text-muted uk-text-center">{{ 'Please select a form.' | trans }}</h3>

            <pre>
                {{ values | json }}
            </pre>

        </div>

        <h3 v-else class="uk-h1 uk-text-muted uk-text-center">{{ 'No forms found.' | trans }}</h3>

    </div>

</template>

<script>

    export default {

        props: ['form', 'roles'],

        data: () => ({
            test: '',
            values: {},
            categories: [],
            selected: [],
            search: '',
            clicks: 0
        }),

        watch: {
            values: {
                handler (values, old) {
                    this.$dispatch('change', values);
                },
                deep: true
            },
            categories (arr, old) {
                this.$session.set(this.$options.name + '.categories', arr);
            },
            selected (arr, old) {
                this.$session.set(this.$options.name + '.selected', arr);
            }
        },

        compiled () {
            this.categories = this.$session.get(this.$options.name + '.categories', []);
            this.selected = this.$session.get(this.$options.name + '.selected', []);
        },

        methods: {

            isActive (prop, value) {
                return _.includes(this[prop], value);
            },

            // TODO enhance double click behaviour
            click (prop, value) {
                this.clicks++;
                if (this.clicks == 1) {
                    this.toggle(prop,value);
                    setTimeout(() => {
                        if (this.clicks == 1) {
                            this[prop] = [value];
                        }
                        this.clicks = 0;
                    }, 250);
                }
            },

            toggle (prop, value) {
                if (_.includes(this[prop], value)) {
                    this[prop] = _.without(this[prop], value);
                }
                else {
                    this[prop].push(value);
                }
            },

            setValues (values) {
                if (!_.isEmpty(values)) this.values = values;
            },

            build (forms) {
                let result = [], fieldsets, path;
                _.each(forms, (form, _form) => {
                    // add form if include property not set or include if form matches the allowed include
                    // allows including forms dependend on node or widget type
                    fieldsets = form.fieldsets;
                    form.fieldsets = [];
                    _.each(fieldsets, (fieldset, _fieldset) => {
                        path = _form+'.'+_fieldset;
                        if (_.has(fieldset, 'inherit') && !_.has(this.values, path+'.inherit')) {
                            this.$set('values.'+path, {inherit: {
                                enabled: true,
                                path: fieldset.inherit.path
                            }});
                            // TODO remove inherit key of something in settings change?
                        }
                        if (!_.has(this.values, path)) this.$set('values.'+path, {});
                        fieldset.name = _fieldset;
                        form.fieldsets.push(fieldset);
                    });
                    form.name = _form;
                    result.push(form);
                });
                return result;
            },

            doInherit(fieldset) {
                return _.has(fieldset, 'inherit');
            },

            isInherited(form, fieldset) {
                return this.$get('values.'+form+'.'+fieldset+'.inherit.enabled', false);
            }

        },

        computed: {

            forms () {
                return [];
            },

            categoryNav () {
                let categories = [];
                _.each(this.forms, (form) => {
                    if (_.has(form, 'categories')) {
                        categories = _.union(categories, form.categories);
                    }
                });
                return categories;
            },

            selectNav () {
                let nav = [];
                _.each(this.filteredByCategories, (form) => {
                    nav.push({
                        label: form.label,
                        name: form.name
                    });
                });
                return nav;
            },

            filteredByCategories () {
                if (!this.categoryNav.length) return this.forms;
                let int;
                // forms filtered by categories
                return _.filter(this.forms, (form) => {
                    int = _.intersection(form.categories, this.categories);
                    return int.length;
                });
            },

            active () {
                // filter forms by search, otherwise via categories and selected
                if (this.search) {
                    let n = 0, searches = ['label', 'name', 'help'];
                    return _.filter(this.forms, (form) => {
                        n = 0;
                        _.each(searches, (prop) => {
                            if (_.has(form, prop)) {
                                if (form[prop].search(this.search) >= 0) n++;
                            }
                        });
                        return n;
                    });
                }
                else  {
                    // selected filter on top of categories filter
                    return _.filter(this.filteredByCategories, (form) => {
                        return _.includes(this.selected, form.name);
                    });
                }
            }

        },

        components: {
            fields: require('./fields.vue')
        },

        partials: {
            toolbar: '<!-- Toolbar -->'
        }
    }

</script>
