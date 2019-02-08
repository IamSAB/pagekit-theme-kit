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
                <li v-for="item in categoryNav" :class="isActive('categories', item) ? 'uk-active' : ''">
                    <a @click="toggle('categories', item)">{{ item }}</a>
                </li>
            </nav>

            <nav class="uk-subnav uk-subnav-pill uk-margin-bottom uk-margin-small-top">
                <li class="uk-disabled">
                    <a>Selected</a>
                </li>
                <li v-for="item in selectNav" :class="isActive('selected', item.name) ? 'uk-active' : ''">
                    <a @click="toggle('selected', item.name)">{{ item.label }}</a>
                </li>
            </nav>

            <div class="uk-panel uk-panel-divider" v-for="form in active" :key="form.name">
                <form class="uk-form-horizontal">
                    <h1>{{ form.label }}</h1>
                    <p v-if="form.help">{{ form.help }}</p>
                    <fieldset v-for="fieldset in form.fieldsets" :key="fieldset.name" style="margin: 15px 0;">
                        <legend>
                            {{ fieldset.label }}
                            <label v-if="inherit">
                                 | <input type="checkbox" v-model="values[form.name][fieldset.name].inherit">
                                <small>Inherit</small>
                            </label>
                        </legend>
                        <p v-if="fieldset.help">{{ fieldset.help }}</p>
                        <fields v-if="!inherit || !values[form.name][fieldset.name].inherit" :config="fieldset.fields" :values="values"></fields>
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
            values: {},
            forms: [],
            categories: [],
            selected: [],
            search: '',
            inherit: true
        }),

        ready () {
            this.$watch('values', (values, old) => {
                this.$dispatch('change', values);
            },{deep:true});
        },

        methods: {

            isActive (prop, value) {
                return _.includes(this[prop], value);
            },

            toggle (prop, value) {
                if (_.includes(this[prop], value)) {
                    this[prop] = _.without(this[prop], value);
                }
                else {
                    this[prop].push(value);
                }
            },

            build (forms, include) {
                let fieldsets, path;
                _.each(forms, (form, _form) => {
                    // add form if include property not set or include if form matches the allowed include
                    // allows including forms dependend on node or widget type
                    if (!_.has(form, include[0]) || (_.has(form, include[0]) && _.includes(form[include[0]], include[1]))) {
                        fieldsets = form.fieldsets;
                        form.fieldsets = [];
                        _.each(fieldsets, (fieldset, _fieldset) => {
                            console.log(_fieldset);
                            console.log(fieldset);
                            // set inherit key if required
                            path = 'values.'+_form+'.'+_fieldset+'.inherit';
                            if (this.inherit && !_.has(this, path)) {
                                this.$set(path, true);
                            }

                            fieldset.fields = _.mapKeys(fieldset.fields, (field, _field) => {
                                return _form+'.'+_fieldset+'.'+_field;
                            });

                            fieldset.name = _fieldset;
                            form.fieldsets.push(fieldset);
                        });
                        form.name = _form;
                        this.forms.push(form);
                    }
                });
            },

            isInherit (_form, _fieldset) {
                return this.inherit && !_.has(this, 'values.'+_form+'.'+_fieldset+'.inherit');
            }

        },

        computed: {

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
            toolbar: '<!-- Toolbar for Saving (required in site settings) -->'
        }
    }

</script>
