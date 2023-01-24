<template>
    <form v-csrf-token v-on:submit.prevent="placeOrder" method="post" class="needs-validation" novalidate>
        <!-- <input type="hidden" name="_token" v-bind:value="csrf"> -->
        <div class="row">
            <div class="col-md-12">
                <div v-bind:class="{'form-group': true, 'has-error':'errors.state'}"  class="mb-3">
                    <label for="orderCategory" class="form-label">Category</label>
                    <select class="form-select" v-model="orderCategory" id="orderCategory" name="orderCategory" @change="fetchOrderService()" required>
                        <option selected disabled value="">Select a category</option>
                        <option v-for="orderCategory in orderCategories" :value="orderCategory.id" v-text="orderCategory.name" v-bind:key="orderCategory.id"></option>
                    </select>
                    <div class="invalid-feedback">
                        {{ errors.get('orderCategory')}}
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div v-bind:class="{'form-group': true, 'has-error':'errors.lga'}" class="mb-3">
                    <label for="orderService" class="form-label">Service</label>
                    <select class="form-select" v-model="orderService" id="orderService" name="orderService" @change="setOrder()" required>
                        <option selected disabled value="">Select category first</option>
                        <option v-for="orderService in orderServices" :value="orderService" v-text="orderService.id + '-' + orderService.name + ' - ' + orderService.per_1000 + ' per 1000'" v-bind:key="orderService.id" v-on:change="clearError()">
                        </option>
                    </select>
                    <div class="invalid-feedback">
                        {{ errors.get('orderService')}}
                    </div>
                </div>
            </div>
            <!-- Condition for normal order start -->
            <div>
                <!-- v-if="orderCategory.subscription_allowed == 1" -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="newOrderDescription" class="form-label">Description</label>
                        <div>
                            <textarea id="newOrderDescription" v-model="newOrderDescription" v-text="setNewOrderDescription" required class="form-control" rows="6"
                                readonly></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="orderLink" class="form-label">Link</label>
                        <input type="text" class="form-control" id="orderLink" v-model="orderLink" required>
                        <div class="invalid-feedback">
                            {{errors.get('additional_street_address')}}
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="orderQuantity" class="form-label">Quantity</label>
                        <input type="text" class="form-control" id="orderQuantity" v-model="orderQuantity" required>
                        <p class="text-muted">Min:10 - Max:50</p>
                        <div class="invalid-feedback">
                            {{errors.get('additional_street_address')}}
                        </div>
                    </div>
                </div>

                <div v-if="isCustomComment" class="col-md-12">
                    <div class="mb-3">
                        <label for="newOrderDescription" class="form-label">Link</label>
                        <div>
                            <textarea id="customComments" v-model="customComments" class="form-control"
                                rows="10"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="orderCharge" class="form-label">Charge</label>
                        <input type="text" class="form-control" id="orderCharge" v-text="setOrderPrice" name="orderCharge" readonly>
                        <div class="invalid-feedback">
                            {{errors.get('additional_street_address')}}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Condition for normal order ends -->

            <!-- Condition For auto subscription starts -->
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Place Order</button>
        </div>
    </form>
</template>
<script>

class Errors{
    constructor(){
        this.errors = {}
    }

    get(field){
        if(this.errors[field]){
            return this.errors[field][0];
        }
    }

    record(errors){
        this.errors = errors.errors;
    }
}

    export default {
        props: ['productId', 'status'],

        mounted() {
            console.log('Component mounted.')
        },

        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                statusChange: this.status,
                orderCategories: [],
                orderServices: [],
                errors: new Errors(),
                orderCategory:'Choose state',
                orderService:'Choose category area',
                orderQuantity:0,
                orderLink:'',
                orderCharge:0,
                newOrderDescription:'',
                customComments:'',
                isCustomComment: false,
                convert: ''
            }
        },
        components:{},
        created(){
            this.fetchOrderCategory();
            // this.fetchLGAreas();
        },


        methods: {
            clearError(){
                console.log('ksksk')
            },
            fetchOrderCategory(){
                axios.get('order/category').then(response => {
                    this.orderCategories = response.data
                });
            },
            fetchOrderService(){
                axios.get('order/service/' + this.orderCategory).then(response => {
                    this.orderServices = response.data
                });
            },
            placeOrder() {
                axios.post('/add-shipping-address', {
                    quantity:this.quantity,
                    link:this.link,
                    dripfeed:this.dripfeedselect,
                    runs:this.runs,
                    interval:this.interval,
                    autolike:this.autolike,
                    username:this.username,
                    minqty:this.minqty,
                    maxqty:this.maxqty,
                    oldPosts:this.postcount,
                    NewPosts:this.postcount,
                    })
                .then(function (response) {
                        response.data.type == 'success' ? toastr.success(response.data.message) : toastr.warning(response.data.message);
                        window.location = '/';
                }).catch(error => {
                        this.errors.record(error.response.data)
                    }
                    );
            }
        },

        computed: {
            // setOrderPrice() {
            //     this.orderPrice = orderService.price_per_item * this.orderQuantity
            // },
            // setNewOrderDescription() {
            //     this.newOrderDescription = orderService.description
            // },
            // setIsCustomComment() {
            //     // this.orderService = orderService.id,
            //     this.isCustomComment = orderService.custom_comments == 1 ? true : false
            // },

            btnStyles() {
                return {
                    'btn-success': (this.statusChange == 1 ? true : false),
                    'btn-danger': (this.statusChange != 1 ? true : false)
                }
            },
        }
    }
</script>
