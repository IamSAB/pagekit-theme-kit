import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: 'Node',
        icon: 'pk-icon-large-brush',
        priority: 16
    },

    created () {
        console.log(JSON.parse(JSON.stringify(window.$themeKit.node)));
        this.setValues(window.$themeKit.node);
        this.built = this.build(window.$configNode);
    },

    extends: Forms,

    computed: {
        forms () {
            return this.built;
        }
    },

    events: {

        save () {
            this.$http.post('admin/system/settings/config', {
                name: 'theme-kit',
                config: {node: this.values}
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

window.Site.components['settings-theme-node'] = Theme;

export default Theme;
