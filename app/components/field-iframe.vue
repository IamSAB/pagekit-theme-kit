<template>

    <div>

        <div class="uk-form-width-large">
            <iframe class="uk-width-1-1" v-if="valid" :src="value"></iframe>
            <div v-else class="uk-placeholder uk-text-center uk-display-block uk-margin-remove">
                <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-icon.svg')">
                <p class="uk-text-muted uk-margin-small-top">{{ 'Enter a source' | trans }}</p>
            </div>
        </div>

        <div class="uk-form-width-large uk-form-icon uk-margin-small-top">
            <i :class="icon"></i>
            <input class="uk-width-1-1" type="text" v-model="value" debounce="500">
        </div>
    </div>

</template>

<script>

    export default {

        computed: {
            icon () {
                const sources = [
                    {
                        icon: 'youtube',
                        regexp: new RegExp(/^https:\/\/(?:www\.)?youtube.com\/embed\/[A-z0-9]+/),
                    },
                    {
                        icon: 'vimeo',
                        regexp: new RegExp(/^https:\/\/player\.vimeo\.com\/video\/[0-9]+/),
                    },
                    {
                        title: 'google',
                        regexp: new RegExp(/^https:\/\/(?:www\.)?google\.com\/maps\/embed\/v1\/(place|search|view|directions|streetview)\?.*(key=.*)/),
                    },
                    {
                        icon: 'google',
                        regexp: new RegExp(/^https:\/\/maps\.googleapis\.com\/maps\/api\/staticmap\?.*(key=.*)/),
                    },
                    {
                        icon: 'check',
                        regexp: new RegExp(/^http(s)?:\/\/[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=.]+$/)
                    }
                ];

                let icon = 'uk-icon-warning';

                _.each(sources, (source) => {
                    if (this.value.match(source.regexp)) {
                        icon = 'uk-icon-'+source.icon;
                        return false;
                    }
                });

                return icon;
            },

            valid () {
                return this.icon != 'uk-icon-warning';
            }
        }

    };

</script>
