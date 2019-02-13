import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: "Theme",
        priority: 11

    },

    created () {
        this.setValues(this.widget.theme);
        this.built = this.build(window.$config);
    },

    computed: {

        forms () {
            let position, type;
            return _.filter(this.built, (form) => {
                position = true; type = true;
                if (_.has(form, 'positions')) position = _.includes(form.positions, this.widget.position);
                if (_.has(form, 'types')) type = _.includes(form.types, this.widget.type);
                return position && type;
            });
        }

    },

    extends: Forms,

    props: {
        widget: Object,
    },

    events: {
        change (values) {
            this.widget.theme = values;
        }
    }
}

window.Widgets.components['widget-theme'] = Theme;

export default Theme;