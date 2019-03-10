<template>

    <div>

        <div v-if="forms.length" :id="$options.name+'-top'">

            <partial name="toolbar"></partial>

            <nav class="uk-subnav uk-subnav-pill uk-margin" v-if="categoriesNav.length">
                <li class="uk-disabled">
                    <a>Categories</a>
                </li>
                <li v-for="item in categoriesNav" :class="item == category ? 'uk-active' : ''" :key="item">
                    <a @click="category = item">{{ item }}</a>
                </li>
            </nav>

            <nav class="uk-subnav uk-subnav-pill uk-margin" v-if="formsNav.length">
                <li class="uk-disabled">
                    <a>Forms</a>
                </li>
                <li v-for="item in formsNav" :class="item.name == selected ? 'uk-active' : ''" :key="item.name">
                    <a @click="selected = item.name">{{ item.label }}</a>
                </li>
            </nav>

        </div>

        <div v-if="forms.length" class="uk-margin-large">

            <h3 v-if="!active" class="uk-h1 uk-text-muted uk-text-center">{{ 'Please select a form.' | trans }}</h3>

            <div v-else>
                <h1 id="theme-form">{{ active.label }}</h1>
                <p v-if="active.help">{{ active.help }}</p>
                <label v-if="active.inherit">
                    <input class="uk-margin-small-right" type="checkbox" v-model="values[active.name].inherit">
                    {{ 'Inherit' | trans }}
                </label>
                <span v-if="values[active.name].inherit"> | <b>See</b> Site > Settings </span>
                <div v-else class="uk-grid uk-margin-top">
                    <div class="uk-width-8-10 uk-width-medium-9-10">
                        <div class="uk-form-horizontal">
                            <fieldset :id="$options.name + fieldset.name" v-for="fieldset in active.fieldsets | orderBy 'label'" :key="fieldset.name">
                                <legend>
                                    {{ fieldset.label }}
                                </legend>
                                <label class="inherit" v-if="fieldset.inherit">
                                    <input class="uk-margin-small-right" type="checkbox" v-model="values[active.name][fieldset.name].inherit">
                                    {{ 'Inherit' | trans }}
                                </label>
                                <span class="uk-margin-small-bottom" v-if="fieldset.help">{{ fieldset.help }}</span>
                                <span v-if="fieldset.inherit && values[active.name][fieldset.name].inherit"><b>See</b> Site > Settings > Fieldsets</span>
                                <fields v-else :config="fieldset.fields" :values="values[active.name][fieldset.name]"></fields>
                            </fieldset>
                        </div>
                    </div>
                    <div class="uk-width-2-10 uk-width-medium-1-10" v-if="fieldsetsNav.length">
                        <div data-uk-sticky="{top: 20, boundary: true}">
                            <nav class="uk-nav uk-nav-side uk-text-center" data-uk-scrollspy-nav="{closest: 'li', smoothscroll: true}">
                                <li>
                                    <a :href="'#'+$options.name+'-top'"><i class="uk-icon-chevron-circle-up"></i></a>
                                </li>
                                <li v-for="item in fieldsetsNav">
                                    <a :href="'#'+item.id">{{ item.label }}</a>
                                </li>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3 v-else class="uk-h1 uk-text-muted uk-text-center">{{ 'No forms found.' | trans }}</h3>

        <style>
            .uk-form fieldset {
                position: relative;
                border: 3px solid #e5e5e5;
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
            .uk-form-help-inline {
                display: inline !important;
                word-wrap: break-word;
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
            category: '',
            selected: ''
        }),

        watch: {
            values: {
                handler (values, old) {
                    this.$dispatch('change', values);
                },
                deep: true
            },
            category (value, old) {
                this.$session.set(this.$options.name + '.category', value);
            },
            selected (value, old) {
                this.$session.set(this.$options.name + '.selected', value);
            }
        },

        ready () {
            this.category = this.$session.get(this.$options.name + '.category', '');
            this.selected = this.$session.get(this.$options.name + '.selected', '');
        },

        methods: {

            setValues (values) {
                if (!_.isEmpty(values)) this.values = values;
            },

            build (forms) {
                let result = [], fieldsets, path;
                _.each(forms, (form, _form) => {
                    fieldsets = form.fieldsets;
                    form.fieldsets = [];
                    if (form.inherit && !_.has(this.values, _form+'.inherit')) {
                        this.$set('values.'+_form+'.inherit', true);
                    }
                    _.each(fieldsets, (fieldset, _fieldset) => {
                        path = _form+'.'+_fieldset;
                        // Pagekit converts empty objects to empty array on serialization, which breaks vue-form.
                        // Necessary to re-set fieldset value if it's empty array
                        if (_.isEmpty(_.get(this.values, _form+'.'+_fieldset), [])) this.$set('values.'+_form+'.'+_fieldset, {});
                        if (fieldset.inherit && !_.has(this.values, _form+'.'+_fieldset+'.inherit')) {
                            this.$set('values.'+_form+'.'+_fieldset+'.inherit', true);
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

            active () {
                return this.selected ? _.find(this.forms, {name: this.selected}) : null;
            },

            categoriesNav () {
                let categories = [];
                _.each(this.forms, (form) => {
                    if (_.has(form, 'categories')) {
                        categories = _.union(categories, form.categories);
                    }
                });
                return categories;
            },

            formsNav () {
                let nav = [];
                _.each(this.forms, (form) => {
                    if (this.categoriesNav.length && _.has(form, 'categories')) {
                        if (_.includes(form.categories, this.category)) {
                            nav.push({
                                label: form.label,
                                name: form.name
                            });
                        }
                    }
                    else {
                        nav.push({
                            label: form.label,
                            name: form.name
                        });
                    }
                });
                return nav;
            },

            fieldsetsNav () {
                let nav = [];
                if (this.active && this.selected) {
                    _.each(this.active.fieldsets, (fieldset) => {
                        nav.push({
                            letter: fieldset.label.substring(0,1),
                            id: this.$options.name + fieldset.name,
                            label: ''
                        });
                    });
                    nav = _.sortBy(nav, 'letter');
                    let before = '';
                    _.each(nav, (item) => {
                        if (before == item.letter) item.label = '<>';
                        else item.label = item.letter
                        before = item.letter;
                    })
                }
                return nav;
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
