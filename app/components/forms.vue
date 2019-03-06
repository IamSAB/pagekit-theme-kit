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
                    <label v-if="form.inherit">
                        <input class="uk-margin-small-right" type="checkbox" v-model="values[form.name].inherit">
                        {{ 'Inherit' | trans }}
                    </label>
                    <span v-if="form.inherit && values[form.name].inherit"> | <b>See</b> Site > Settings > Themekit / {{ form.label }} </span>

                    <fieldset v-else v-for="fieldset in form.fieldsets" :key="fieldset.name">
                        <legend>
                            {{ fieldset.label }}
                        </legend>
                        <label class="inherit" v-if="fieldset.inherit">
                            <input class="uk-margin-small-right" type="checkbox" v-model="values[form.name][fieldset.name].inherit">
                            {{ 'Inherit' | trans }}
                        </label>
                        <span class="uk-margin-small-bottom" v-if="fieldset.help">{{ fieldset.help }}</span>
                        <span v-if="fieldset.inherit && values[form.name][fieldset.name].inherit"><b>See</b> Site > Settings > Themekit / Defaults</span>
                        <fields v-else :config="fieldset.fields" :values="values[form.name][fieldset.name]"></fields>
                    </fieldset>
                </form>
            </div>

            <h3 v-if="!active.length" class="uk-h1 uk-text-muted uk-text-center">{{ 'Please select a form.' | trans }}</h3>

            <pre>
                {{ values | json }}
            </pre>

        </div>

        <h3 v-else class="uk-h1 uk-text-muted uk-text-center">{{ 'No forms found.' | trans }}</h3>

        <style>
            .uk-form fieldset {
                position: relative;
                border: 3px solid #e5e5e5;
                margin: initial !important;
                padding: 20px !important;
                margin: 20px 0 !important;
            }
            .uk-form legend {
                width: initial !important;
                border: 3px solid #e5e5e5;
                padding: 3px 9px !important;
                line-height: initial !important;

            }
            .uk-form legend::after {
                content: initial !important;
                display: initial !important;
                border-bottom: initial !important;
                width: initial !important;
            }
            .inherit {
                position: absolute;
                right: 20px;
                top: -35px;
                padding: 8px 12px !important;
                background: #e5e5e5;
            }
            .uk-form-horizontal .uk-form-label {
                width: 150px !important;
            }
            .uk-form-horizontal .uk-form-controls {
                margin-left: 165px;
            }
            .uk-form-help-inline {
                display: inline !important;
            }
        </style>

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
                    fieldsets = form.fieldsets;
                    form.fieldsets = [];
                    if (form.inherit && !_.has('values.'+_form+'.inherit')) {
                        this.$set('values.'+_form+'.inherit', true);
                    }
                    _.each(fieldsets, (fieldset, _fieldset) => {
                        path = _form+'.'+_fieldset;
                        // Pagekit converts empty objects to empty array on serialization, which breaks vue-form.
                        // Necessary to re-set fieldset value if it's empty array
                        if (_.isEmpty(_.get(this.values, path), [])) this.$set('values.'+path, {});

                        if (fieldset.inherit && !_.has('values.'+path+'.inherit')) {
                            this.$set('values.'+path+'.inherit', true);
                        }
                        fieldset.name = _fieldset;
                        form.fieldsets.push(fieldset);
                    });
                    form.name = _form;
                    result.push(form);
                });
                return result;
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
