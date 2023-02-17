<template>

    <form v-on:submit.prevent="sendSupport" method="post" class="needs-validation" novalidate>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="for-number-subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" id="for-number-subject" v-model="subject" readonly>
                <div class="invalid-feedback">
                    {{ errors.get('subject')}}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="supportOrderId" class="form-label">Order ID: [For multiple orders, please separate them using comma.
                    (example: 10867110,10867210,10867500)]</label>
                <input type="text" class="form-control" name="orderId" v-model="orderId" id="supportOrderId">
                <div class="invalid-feedback">
                    {{ errors.get('orderId')}}
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="mb-3">
                <label for="number-request" class="form-label">Request</label>
                <select name="request" v-model="request"  id="for-number-request" class="form-select">
                    <option v-for="request in requestSelect" :value="request" v-bind:key="request">
                        {{request}}
                    </option>
                </select>
                <div class="invalid-feedback">
                    {{ errors.get('request')}}
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label for="message" class="form-label">Message (optional)</label>
                <div>
                    <textarea id="message" name="message" v-model="message" class="form-control" rows="3"></textarea>
                </div>
                <div class="invalid-feedback">
                    {{ errors.get('message')}}
                </div>
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
            statusChange: this.status,
            orderServices: [],
            errors: new Errors(),
            orderCategory: 'Select a category',
            orderService: 'Select a service',

            serviceFeature: 'NO',
            requestSelect: ['Refill','Cancel','Speedup','Restart','Other'],
            subject:'Number',
            orderId:'',
            request:'Refill',
            message:''

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
        clearInput() {
            this.newOrderDescription = ''
            this.isCustomComment = false
            this.pricePerItem = 0
            this.serviceFeature = 'NO'
            this.orderLink = ''
            this.orderQuantity = '',
                this.errors.clear()
        },

        sendSupport() {
            axios.post('/support/ticket/store', {
                subject: this.subject,
                orderId: this.orderId,
                request: this.request,
                message: this.message
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
