<template>

    <form v-on:submit.prevent="sendSupport" method="post" class="needs-validation" novalidate>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="for-order-subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" id="for-order-subject" v-model="subject" readonly>
                <span class="text-danger" role="alert">{{ errors.get('subject')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div v-bind:class="{ 'form-group': true, 'has-error':'errors.orderId'}" class="mb-3">
                <label for="orderId" class="form-label">Order ID: [For multiple orders, please separate them using comma.
                    (example: 10867110,10867210,10867500)]</label>
                <input type="text" class="form-control" name="orderId" v-model="orderId" id="orderId">
                <span class="text-danger" role="alert">{{ errors.get('orderId')}}</span>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="mb-3">
                <label for="request-order" class="form-label">Request</label>
                <select name="request" v-model="request"  id="for-request-order" class="form-select">
                    <option v-for="request in requestSelect" :value="request" v-bind:key="request">
                        {{request}}
                    </option>
                </select>
                <span class="text-danger" role="alert">{{ errors.get('request')}}</span>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="order-description" class="form-label">Description</label>
                <div>
                    <textarea id="order-description" name="description" v-model="description" class="form-control" rows="3"></textarea>
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
            serviceFeature: 'NO',
            requestSelect: ['Refill','Cancel','Speedup','Restart','Other'],
            subject:'Order',
            orderId:'',
            request:'Refill',
            description:''

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
                orderId: this.orderId,
                request: this.request,
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
