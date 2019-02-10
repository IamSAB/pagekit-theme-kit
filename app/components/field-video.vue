<template>

    <div>

        <div class="uk-form-width-large">
            <video v-if="valid" :src="$url(value)"></video>
            <div v-else class="uk-placeholder uk-text-center uk-display-block uk-margin-remove">
                <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-video.svg')">
                <p class="uk-text-muted uk-margin-small-top">{{ 'Select or enter a source' | trans }}</p>
            </div>
        </div>

        <div class="uk-form-icon uk-margin-small-top uk-form-width-large">
            <a @click.prevent="$refs.modal.open()" class="uk-icon-pencil" style="
                pointer-events: all;
                right: 0px;
            "></a>
            <input class="uk-width-1-1" v-model="value" type="text" debounce="500" style="
                padding-left: 4px!important;
                padding-right: 30px;
            ">
        </div>

        <v-modal v-ref:modal large>

            <panel-finder :root="pagekit.storage" v-ref:finder :modal="true"></panel-finder>

            <div class="uk-modal-footer uk-text-right">
                <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}</button>
                <button class="uk-button uk-modal-close" type="button" @click.prevent="value = ''">{{ 'Clear' | trans }}</button>
                <button class="uk-button uk-button-primary" type="button" :disabled="!selected" @click.prevent="select">{{ 'Select' | trans }}</button>
            </div>

        </v-modal>
    </div>

</template>

<script>

    export default {

        data: () => ({
            pagekit: $pagekit,
        }),

        computed: {

            selected () {
                var selected = this.$refs.finder.getSelected();
                return selected.length === 1 && this.$refs.finder.isVideo(selected[0])
            },

            valid () {
                return this.value.match(new RegExp(/\.(mpeg|ogv|mp4|webm|wmv)$/));
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
