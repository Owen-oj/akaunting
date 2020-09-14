<template>
    <div>
        <div class="card">
            <div class="card-body">
                <el-steps :active="active" finish-status="success">
                    <el-step :title="steps.employees.title"></el-step>
                    <el-step :title="steps.variables.title"></el-step>
                    <el-step :title="steps.pay_slips.title"></el-step>
                    <el-step :title="steps.approval.title"></el-step>
                </el-steps>
            </div>
        </div>

        <form id="form-step-create" method="POST" action="#"/>
        <component v-bind:is="stepComponent" @confirm="onSubmit"></component>
    </div>
</template>


<script>
    import Vue from 'vue';

    import AkauntingSearch from './../../../../../../resources/assets/js/components/AkauntingSearch';
    import AkauntingModal from './../../../../../../resources/assets/js/components/AkauntingModal';
    import AkauntingModalAddNew from './../../../../../../resources/assets/js/components/AkauntingModalAddNew';
    import AkauntingRadioGroup from './../../../../../../resources/assets/js/components/forms/AkauntingRadioGroup';
    import AkauntingSelect from './../../../../../../resources/assets/js/components/AkauntingSelect';
    import AkauntingSelectRemote from './../../../../../../resources/assets/js/components/AkauntingSelectRemote';
    import AkauntingDate from './../../../../../../resources/assets/js/components/AkauntingDate';
    import AkauntingRecurring from './../../../../../../resources/assets/js/components/AkauntingRecurring';
    import Form from './../../../../../../resources/assets/js/plugins/form';
    import {VMoney} from 'v-money';
    import { Select, Option, Steps, Step, Button, Collapse, CollapseItem } from 'element-ui';

    export default {
        name: "payroll-run-payroll",

        components: {
            AkauntingSearch,
            AkauntingRadioGroup,
            AkauntingSelect,
            AkauntingSelectRemote,
            AkauntingModal,
            AkauntingModalAddNew,
            AkauntingDate,
            AkauntingRecurring,
            [Select.name]: Select,
            [Option.name]: Option,
            [Steps.name]: Steps,
            [Step.name]: Step,
            [Button.name]: Button,
            [Collapse.name]: Collapse,
            [CollapseItem.name]: CollapseItem,
        },

        directives: {
            money: VMoney
        },

        props: {
            firstPath: {
                type: String,
                default: '',
                description: "Get first step html"
            },

            payCalendarId: {
                type: Number,
                default: 0,
                description: "Get Pay Calendar Id"
            },

            startStep: {
                type: Number,
                default: 0,
                description: "Step start number"
            },

            steps: {
                type: Object,
                default: null,
                description: "Steps data"
            },

            data: {
                type: Array,
                default: null,
                description: "Timelines data"
            },

            deleteText: {
                type: String,
                default: 'Delete Activity',
                description: "Show Delete Modal Title"
            },

            deleteTextMessage: {
                type: String,
                default: 'Are you sure?',
                description: "Show Delete Moda Message"
            },

            editButtonStatus: {
                type: Boolean,
                default: false,
                description: "Edit Button show and action"
            },

            deleteButtonStatus: {
                type: Boolean,
                default: false,
                description: "Delete Button show and action"
            },

            statusText: {
                type: String,
                default: 'Status',
                description: "Status Text"
            },

            showButtonText: {
                type: String,
                default: 'Show',
                description: "Show Button Text"
            },

            editButtonText: {
                type: String,
                default: 'Edit',
                description: "Edit Button Text"
            },

            deleteButtonText: {
                type: String,
                default: 'Delete',
                description: "Delete Button Text"
            },

            saveButtonText: {
                type: String,
                default: 'Save',
                description: "Save Button Text"
            },

            cancelButtonText: {
                type: String,
                default: 'Cancel',
                description: "Cancel Button Text"
            },

            noRecordsText: {
                type: String,
                default: 'No Records',
                description: "No Records Text"
            },
        },

        data() {
            return {
                active: this.startStep,
                form: {},
                stepComponent: null,
                money: {
                    decimal: '.',
                    thousands: ',',
                    prefix: '$ ',
                    suffix: '',
                    precision: 2,
                    masked: false /* doesn't work with directive */
                },
            }
        },

        created: function() {
            this.form = new Form('form-step-create');

            axios.get(this.firstPath)
            .then(response => {
                let html = response.data.html;

                if (html == undefined) {
                    window.location.reload();
                }

                this.stepComponent = Vue.component('add-new-component', (resolve, reject) => {
                    resolve({
                        template : '<div id="run-payroll-component">' + html + '</div>',

                        components: {
                            AkauntingSearch,
                            AkauntingRadioGroup,
                            AkauntingSelect,
                            AkauntingSelectRemote,
                            AkauntingModal,
                            AkauntingModalAddNew,
                            AkauntingDate,
                            AkauntingRecurring,
                            [Select.name]: Select,
                            [Option.name]: Option,
                            [Steps.name]: Steps,
                            [Step.name]: Step,
                            [Button.name]: Button,
                            [Collapse.name]: Collapse,
                            [CollapseItem.name]: CollapseItem,
                        },

                        created: function() {
                            this.form = new Form('form-step-create');
                        },

                        mounted() {
                            let form_id = document.getElementById('run-payroll-component').children[0].id;

                            this.form = new Form(form_id);

                            this.$vnode.componentInstance.$children.forEach((select, index) => {
                                if (select.options) {
                                    this.form[select.name] = select.real_model;
                                }
                            });
                        },

                        data: function () {
                            return {
                                form: {},
                            }
                        },

                        methods: {
                            onSubmit() {
                                this.$emit('confirm', this.form);
                            }
                        }
                    })
                });
            })
            .catch(e => {
                window.location.reload();
            })
            .finally(function () {
                // always executed
            });
        },

        mounted() {

        },

        methods: {
            onSubmit(form) {
                form.loading = true;

                axios[form.method](form.action, form.data())
                .then(response => {
                    form.errors.clear();

                    form.loading = false;

                    if (response.data.redirect) {
                        form.loading = true;

                        this.active++;
                        if (this.active < 4) {
                            this.nextStep(response.data.redirect);
                        } else {
                            window.location.href = response.data.redirect;
                        }
                    }

                    form.response = response.data;
                })
                .catch(form.onFail.bind(this));
            },

            nextStep(path) {
                this.form = new Form('form-step-create');

                axios.get(path)
                .then(response => {
                    let html = response.data.html;

                    this.stepComponent = Vue.component('add-new-component', (resolve, reject) => {
                        resolve({
                            template : '<div id="run-payroll-component">' + html + '</div>',

                            components: {
                                AkauntingSearch,
                                AkauntingRadioGroup,
                                AkauntingSelect,
                                AkauntingSelectRemote,
                                AkauntingModal,
                                AkauntingModalAddNew,
                                AkauntingDate,
                                AkauntingRecurring,
                                [Select.name]: Select,
                                [Option.name]: Option,
                                [Steps.name]: Steps,
                                [Step.name]: Step,
                                [Button.name]: Button,
                                [Collapse.name]: Collapse,
                                [CollapseItem.name]: CollapseItem,
                            },

                            created: function() {
                                this.form = new Form('form-step-create');
                            },

                            mounted() {
                                let form_id = document.getElementById('run-payroll-component').children[0].id;

                                this.form = new Form(form_id);

                                this.$vnode.componentInstance.$children.forEach((select, index) => {
                                    if (select.options) {
                                        this.form[select.name] = select.real_model;
                                    }
                                });
                            },

                            data: function () {
                                return {
                                    form: {},
                                    employee: {},
                                    benefits: {},
                                    deductions: {},
                                    variables: {
                                        employee: {
                                            salary : '0$',
                                            benefits : '0$',
                                            deductions : '0$',
                                            total : '0$',
                                        }
                                    },
                                    pay_slips: {
                                        employee: {
                                            payment_date : '-',
                                            tax_number : '-',
                                            bank_account : '-',
                                            payment_method : '-',
                                            position : '-',
                                            from_date : '-',
                                            to_date : '-',
                                            salary : null,
                                            benefits : [],
                                            deductions : [],
                                            total : '-',
                                        }
                                    }
                                }
                            },

                            methods: {
                                onSubmit() {
                                    this.$emit('confirm', this.form);
                                },

                                onChangeEmployee(employee_id) {
                                    axios.get(url + '/payroll/run-payrolls/' + this.form.run_payroll_id + '/employees/' + employee_id)
                                    .then(response => {
                                        this.variables.employee.salary = response.data.data.salary;
                                        this.variables.employee.benefits = response.data.data.total_benefit;
                                        this.variables.employee.deductions = response.data.data.total_deduction;
                                        this.variables.employee.total = response.data.data.total_amount;

                                        this.benefits = response.data.data.benefits;
                                        this.deductions = response.data.data.deductions;
                                    })
                                    .catch(e => {
                                    })
                                    .finally(function () {
                                        // always executed
                                    });
                                },

                                addBenefit() {

                                },

                                addDeduction() {

                                },

                                onChangePaySlipEmployee(employee_id) {
                                    axios.get(url + '/payroll/pay-calendars/' + this.form.pay_calendar_id + '/run-payrolls/' + this.form.run_payroll_id + '/pay-slips/employees/' + employee_id)
                                    .then(response => {
                                        this.pay_slips.employee.payment_date = response.data.data.payment_date;
                                        this.pay_slips.employee.tax_number = response.data.data.tax_number;
                                        this.pay_slips.employee.bank_number = response.data.data.bank_number;
                                        this.pay_slips.employee.payment_method = response.data.data.payment_method;
                                        this.pay_slips.employee.position = response.data.data.position;
                                        this.pay_slips.employee.from_date = response.data.data.from_date;
                                        this.pay_slips.employee.to_date = response.data.data.to_date;
                                        this.pay_slips.employee.salary = response.data.data.salary;
                                        this.pay_slips.employee.benefits = response.data.data.benefits;
                                        this.pay_slips.employee.deductions = response.data.data.deductions;
                                        this.pay_slips.employee.total = response.data.data.total;
                                    })
                                    .catch(e => {
                                    })
                                    .finally(function () {
                                        // always executed
                                    });
                                },

                                onPrintPaySlipEmployee() {
                                    let redirect = url + '/payroll/pay-calendars/' + this.form.pay_calendar_id + '/run-payrolls/' + this.form.run_payroll_id + '/pay-slips/' + this.form.employee + '/print';

                                    window.open(redirect, '_blank');
                                }
                            }
                        })
                    });
                })
                .catch(e => {
                    this.errors.push(e);
                })
                .finally(function () {
                    // always executed
                });
            }
        },

        watch: {
        },
    }
</script>
