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
        console.log(JSON.parse(JSON.stringify(this.node.theme)));
        this.setValues(this.node.theme);
        console.log(JSON.parse(JSON.stringify(this.values)));

        this.built = this.build(window.$config);
        console.log(JSON.parse(JSON.stringify(this.node.theme)));
        this.categories = ['Position'];
    },

    computed: {

        forms () {
            return _.filter(this.built, (form) => {
                if (_.has(form, 'types')) return _.includes(form.types, this.node.type);
                else return true;
            });
        }

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
