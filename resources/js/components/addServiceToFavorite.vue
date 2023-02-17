<template>
    <i @click="packageFavorite" v-bind:class="favoriteStyles"></i>
</template>
<script>
export default {
    props: ['packageId', 'status'],

    mounted() {
        // console.log('Component mounted.')
    },

    data: function name(params) {
        return {
            statusChange: this.status,
        }
    },

    methods: {
        packageFavorite() {
            axios.post('/addtofavorite',{
                'pid': this.packageId
            })
                .then(response => {
                    response.data == true ? toastr.success('Service ID: ' + this.packageId + ' has been added to favourite services') : toastr.warning('Service ID: ' + this.packageId +' has been removed to favourite services');
                    // this.$root.$refs.A.reRender();
                    // this.$emit('my-custom-event')
                    this.statusChange = response.data == true ? 1 : 0;
                })

            // .catch(errors =>{
            //     if(errors.response.status == 401){
            //         toastr.warning('You need to log in to save this product');
            //         window.location = '/login';
            //     }
            // });
        }
    },

    computed: {
        favoriteStyles() {
            return {
                'fas fa-heart': (this.statusChange == 1 ? true : false),
                'far fa-heart': (this.statusChange != 1 ? true : false)
            }
        },
    }
}
</script>
