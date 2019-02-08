<template>

    <div>
        <div v-if="!value" class="uk-form-width-large" style="max-width: 100%;">
            <a class="uk-placeholder uk-text-center uk-display-block uk-margin-remove" style="max-width: 100%;" @click.prevent="$refs.modal.open()">
                <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-image.svg')">
                <p class="uk-text-muted uk-margin-small-top">{{ 'Select Image' | trans }}</p>
            </a>
        </div>
        <template v-else>
            <img class="uk-form-width-large" :src="value.indexOf('blob:') !== 0  ? $url(value) : value">
            <div>
                <input class="uk-margin-small-top uk-form-width-large" type="text" :value="value" disabled>
                <a class="pk-icon-delete" @click.prevent="value = ''" data-uk-tooltip :title="'Delete source' | trans"></a>
            </div>
        </template>

        <v-modal v-ref:modal large>

            <panel-finder :root="pagekit.storage" v-ref:finder :modal="true"></panel-finder>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-button-primary" type="button" :disabled="!selected" @click.prevent="select">{{ 'Select' | trans }}</button>
            </div>

        </v-modal>
    </div>

</template>

<script>

    module.exports = {

        data: () => ({
            pagekit: $pagekit,
        }),

        computed: {

            selected () {
                var selected = this.$refs.finder.getSelected();
                return selected.length === 1 && this.$refs.finder.isImage(selected[0])
            }

        },

        methods: {

            select: function() {
                this.value = this.$refs.finder.getSelected()[0];
                this.$refs.finder.removeSelection();
                this.$refs.modal.close();
            }

        }

    };

</script>
