import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: 'Theme Kit',
        icon: 'pk-icon-large-brush',
        priority: 16
    },

    created () {
        if (!_.isEmpty(window.$themeKit)) this.values = window.$themeKit;
        this.build(window.$config, () => true);
    },

    extends: Forms,

    events: {

        save () {
            this.$http.post('admin/system/settings/config', {
                name: 'theme-kit',
                config: this.values
            }).catch((res) => {
                this.$notify(res.data, 'danger');
            });
        }

    },

    partials: {
        toolbar: `<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap">
                    <div class="uk-margin-small-top">
                        <h2 class="uk-margin-remove">Theme Kit</h2>
                    </div>
                    <div class="uk-margin-small-top">
                        <button class="uk-button uk-button-primary" type="submit">Save</button>
                    </div>
                </div>`
    }

};

window.Site.components['settings-theme-kit'] = Theme;

export default Theme;