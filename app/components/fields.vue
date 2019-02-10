<template>

    <div class="uk-form-row">
        <div class="uk-form-row" v-for="field in fields" :key="field.name">
            <label class="uk-form-label">{{ field.label }}</label>
            <div class="uk-form-controls uk-form-controls-condensed">
                <component :is="field.type" :field="field"></component>
                <span v-if="field.help" class="uk-form-help-inline uk-text-muted">{{ field.help }}</span>
                <div v-if="field.responsive" class="uk-flex uk-flex-wrap">
                    <span class="uk-margin-right uk-margin-small-top" v-for="viewport in field.responsive" :key="viewport">
                        {{ viewport }}
                        <component :is="'select'" :field="responsify(field, viewport)"></component>
                    </span>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    export default {

        extends: Vue.component('fields'),

        fields: {
            choose: `<input type="checkbox" v-bind="attrs" v-model="value" v-bind:true-value="field.true" :false-value="\'\'">`,
            image: require('./field-image.vue'),
            video: require('./field-video.vue'),
            iframe: require('./field-iframe.vue'),
            editor: `<v-editor type="field.editor.type" :value.sync="value" :options="field.editor.options"></v-editor>`
        },

        methods: {
            responsify(field, viewport) {
                field = JSON.parse(JSON.stringify(field));
                _.each(field.options, (option, key) => {
                    if (key != '- Select -') field.options[key] = option+viewport;
                });
                field.name = field.name+viewport;
                _.set(field, 'attrs.class', 'uk-form-small');
                return field;
            }
        }
    }

</script>
