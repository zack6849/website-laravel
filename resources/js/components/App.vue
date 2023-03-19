<script>
export default {
    name: 'App',
    data() {
        return {
            shownav: false,
        }
    },
    methods: {
        toggle() {
            this.shownav = !this.shownav;
        }
    }
}
</script>
