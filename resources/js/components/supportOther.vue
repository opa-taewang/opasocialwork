<template>
    <form v-on:submit.prevent="sendSupport" method="post" class="needs-validation" novalidate>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="for-other-subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" id="for-other-subject" v-model="subject" readonly>
                <span class="text-danger" role="alert">{{ errors.get('subject') }}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="other-description" class="form-label">Description</label>
                <div>
                    <textarea id="other-description" name="description" v-model="description" class="form-control"
                        rows="3"></textarea>
                </div>
                <span class="text-danger" role="alert">{{ errors.get('description') }}</span>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary w-md col-md-12">Submit</button>
        </div>
    </form>
</template>
<script>

class Errors {
    constructor() {
        this.errors = {}
    }

    get(field) {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    record(errors) {
        this.errors = errors.errors;
    }

    clear() {
        this.errors = {}
    }
}

export default {
    props: [],

    mounted() {
        // console.log(axios);
        // console.log('Component mounted.')
    },

    data() {
        return {
            // csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            errors: new Errors(),
            subject: 'Other',
            description: ''

        }
    },
    watch: {
        // orderService(){
        //     // console.log(orderService);
        // }
    },
    components: {},
    created() {
        // this.fetchOrderCategory();
        // this.orderCategory = 'Select a category';
    },


    methods: {
        redirectMe() {
            this.$router.push('/');
        },
        sendSupport() {
            axios.post('/support/ticket/store', {
                subject: this.subject,
                description: this.description
            })
                .then(function (response) {
                    // console.log(response.data)
                    // this.redirectMe();
                    // response.data.type == 'success' ? toastr.success(response.data.message) : toastr.warning(response.data.message);
                    // $router.back()
                    window.location.href = '/ticket';
                }).catch(error => {
                    console.log(error.response.data)
                    this.errors.record(error.response.data)
                }
                );
        }
    },
}
</script>
