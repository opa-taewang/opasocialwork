<template>

    <form v-on:submit.prevent="sendSupport" method="post" class="needs-validation" novalidate>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="for-support-subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" id="for-support-subject" v-model="subject" readonly>
                <span class="text-danger" role="alert">{{ errors.get('subject')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="websiteUrl" class="form-label">Website Url</label>
                <input type="text" class="form-control" name="websiteUrl" v-model="websiteUrl" id="websiteUrl">
                <span class="text-danger" role="alert">{{ errors.get('websiteUrl')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="contact" class="form-label">Whatsapp/Instagram/Calling Details</label>
                <input type="text" class="form-control" name="contact" v-model="contact" id="contact">
                <span class="text-danger" role="alert">{{ errors.get('contact')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="monthlySell" class="form-label">Monthly sell</label>
                <input type="text" class="form-control" name="monthlySell" v-model="monthlySell" id="monthlySell">
                <span class="text-danger" role="alert">{{ errors.get('monthlySell')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="serviceId" class="form-label">On which service Id do you need discount' [(example 123,453,999,998,1023,564)]</label>
                <input type="text" class="form-control" name="serviceId" v-model="serviceId" id="serviceId">
                <span class="text-danger" role="alert">{{ errors.get('serviceId')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="api-description" class="form-label">Description</label>
                <div>
                    <textarea id="api-description" name="description" v-model="description" class="form-control" rows="3"></textarea>
                </div>
                <span class="text-danger" role="alert">{{ errors.get('description')}}</span>
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
            subject:'API',
            websiteUrl:'',
            contact:'',
            monthlySell:'',
            serviceId:'',
            description:'',

        }
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
                websiteUrl: this.websiteUrl,
                contact: this.contact,
                monthlySell: this.monthlySell,
                serviceId: this.serviceId,
                description: this.description,
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
