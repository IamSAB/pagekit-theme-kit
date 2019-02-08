import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: "Theme",
        priority: 11

    },

    created () {
        if (!_.isEmpty(this.widget.theme)) this.values = this.widget.theme;
        this.build(window.$config, ['positions', this.widget.position], true);
    },

    extends: Forms,

    props: {
        widget: Object,
    },

    events: {
        change (data) {
            this.widget.theme = data;
        }
    }
}

window.Widgets.components['widget-theme'] = Theme;

export default Theme;