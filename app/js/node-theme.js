import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: "Theme",
        priority: 11

    },

    props: {
        node: Object,
    },

    created () {
        if (!_.isEmpty(this.node.theme)) this.values = this.node.theme;
        this.build(window.$config, ['type', [this.node.type]], true);
    },

    extends: Forms,

    events: {
        change (data) {
            this.node.theme = data;
        }
    }
}

window.Site.components['node-theme'] = Theme;

export default Theme;