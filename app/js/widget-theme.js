import Forms from "../components/forms.vue";

const Theme = {

    section: {
        label: "Theme",
        priority: 11

    },

    created () {
        if (!_.isEmpty(this.widget.theme)) this.values = this.widget.theme;
        this.$watch('widget.position', () => {
            this.build(window.$config, (form) => {
                let position = true, type = true;
                if (_.has(form, 'positions')) position = _.includes(form.positions, this.widget.position);
                if (_.has(form, 'types')) type = _.includes(form.types, this.widget.type);
                return position && type;
            });
        }, {immediate: true});
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