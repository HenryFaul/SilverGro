<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, ref, watch, inject, onMounted} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage, Link} from "@inertiajs/vue3";
import Icon from "@/Components/Icon.vue";
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import {CheckIcon, ChevronUpDownIcon, PaperClipIcon, XCircleIcon} from '@heroicons/vue/20/solid';
import {
    Switch,
    SwitchGroup,
    SwitchLabel,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions
} from '@headlessui/vue'
import DriverVehicleModal from "@/Components/UI/DriverVehicleModal.vue";
import DriverVehicleModalAdd from "@/Components/UI/DriverVehicleModal.vue";
import AssignedCommModal from "@/Components/UI/AssignedCommModal.vue";
import ContractLinkModal from "@/Components/UI/ContractLinkModal.vue";

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';


const props = defineProps({
    transaction: Object,
    all_customers: Object,
    contract_types: Object,
    all_products: Object,
    all_suppliers: Object,
    all_transporters: Object,
    all_staff: Object,
    confirmation_types: Object,
    all_product_sources: Object,
    all_packaging: Object,
    all_billing_units: Object,
    loading_hour_options: Object,
    all_drivers: Object,
    all_vehicles: Object,
    all_transport_rates: Object,
    all_status_entities:Object,
    all_status_types:Object,
    all_invoice_statuses:Object,
    linked_trans:Object,
    rules_with_approvals:Object
});

const swal = inject('$swal');

onMounted(() => {

});


/*'old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
    'product_notes','supplier_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
    'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done'*/


const emptyErrorsTransForm = computed(() => Object.keys(transport_trans_Form.errors).length === 0 && transport_trans_Form.errors.constructor === Object)

let transport_trans_Form = useForm({
    old_id: props.transaction.old_id,
    contract_type_id: props.contract_types.find(element => element.id === props.transaction.contract_type_id),
    contract_no: props.transaction.contract_no,
    product_id: props.all_products.find(element => element.id === props.transaction.product_id),
    supplier_id: props.all_suppliers.find(element => element.id === props.transaction.supplier_id),
    customer_id: props.all_customers.find(element => element.id === props.transaction.customer_id),
    transporter_id: props.all_transporters.find(element => element.id === props.transaction.transporter_id),
    include_in_calculations: props.transaction.include_in_calculations,
    transport_date_earliest: props.transaction.transport_date_earliest,
    transport_date_latest: props.transaction.transport_date_latest,
    suppliers_notes: props.transaction.suppliers_notes,
    delivery_notes: props.transaction.delivery_notes,
    product_notes: props.transaction.product_notes,
    customer_notes: props.transaction.customer_notes,
    traders_notes: props.transaction.traders_notes,
    transport_notes: props.transaction.transport_notes,
    pricing_notes: props.transaction.pricing_notes,
    process_notes: props.transaction.process_notes,
    document_notes: props.transaction.document_notes,
    transaction_notes: props.transaction.transaction_notes,
    traders_notes_supplier: props.transaction.traders_notes_supplier,
    traders_notes_customer: props.transaction.traders_notes_customer,
    traders_notes_transport: props.transaction.traders_notes_transport,
    is_transaction_done: props.transaction.is_transaction_done,
});

/*'transport_trans_id','confirmed_by_id','confirmed_by_type_id',
    'product_id','packaging_incoming_id','packaging_outgoing_id','product_source_id','product_grade_perc','no_units_incoming'
    ,'billing_units_incoming_id','no_units_outgoing','billing_units_outgoing_id','is_weighbridge_certificate_received'
    ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id'*/

const emptyErrorsLoadForm = computed(() => Object.keys(transport_load_Form.errors).length === 0 && transport_load_Form.errors.constructor === Object)

let transport_load_Form = useForm({
    confirmed_by_id: props.all_staff.find(element => element.id === props.transaction.transport_load.confirmed_by_id),
    confirmed_by_type_id: props.confirmation_types.find(element => element.id === props.transaction.transport_load.confirmed_by_type_id),
    packaging_incoming_id: props.all_packaging.find(element => element.id === props.transaction.transport_load.packaging_incoming_id),
    packaging_outgoing_id: props.all_packaging.find(element => element.id === props.transaction.transport_load.packaging_outgoing_id),
    product_source_id: props.all_product_sources.find(element => element.id === props.transaction.transport_load.product_source_id),
    product_grade_perc: props.transaction.transport_load.product_grade_perc,
    no_units_incoming: props.transaction.transport_load.no_units_incoming,
    billing_units_incoming_id: props.all_billing_units.find(element => element.id === props.transaction.transport_load.billing_units_incoming_id),
    no_units_outgoing: props.transaction.transport_load.no_units_outgoing,
    billing_units_outgoing_id: props.all_billing_units.find(element => element.id === props.transaction.transport_load.billing_units_outgoing_id),
    is_weighbridge_certificate_received: props.transaction.transport_load.is_weighbridge_certificate_received,
    delivery_note: props.transaction.transport_load.delivery_note,
    calculated_route_distance: props.transaction.transport_load.calculated_route_distance,
    collection_address_id: transport_trans_Form.supplier_id.addressable.find(element => element.id === props.transaction.transport_load.collection_address_id),
    delivery_address_id: transport_trans_Form.customer_id.addressable.find(element => element.id === props.transaction.transport_load.delivery_address_id),

});

const emptyErrorsJobForm = computed(() => Object.keys(transport_job_Form.errors).length === 0 && transport_job_Form.errors.constructor === Object)

let transport_job_Form = useForm({
    customer_order_number: props.transaction.transport_job.customer_order_number,
    is_multi_loads: props.transaction.transport_job.is_multi_loads,
    is_approved: props.transaction.transport_job.is_approved,
    is_transport_costs_inc_price: props.transaction.transport_job.is_transport_costs_inc_price,
    is_product_zero_rated: props.transaction.transport_job.is_product_zero_rated,
    offloading_hours_from_id: props.transaction.transport_job.offloading_hours_from_id,
    offloading_hours_to_id: props.transaction.transport_job.offloading_hours_to_id,
    loading_hours_from_id: props.transaction.transport_job.loading_hours_from_id,
    loading_hours_to_id: props.transaction.transport_job.loading_hours_to_id,
    load_insurance_per_ton: props.transaction.transport_job.load_insurance_per_ton,
    total_load_insurance: props.transaction.transport_job.total_load_insurance,
    number_loads: props.transaction.transport_job.number_loads,
    loading_instructions: props.transaction.transport_job.loading_instructions,
    offloading_instructions: props.transaction.transport_job.offloading_instructions,
});


/*'transport_trans_id','transport_load_id','transport_rate_basis_id','cost_price_per_unit','cost_price_per_ton','selling_price_per_unit','cost_price',
    'selling_price','selling_price_per_ton','cost_price_per_unit','selling_price_per_unit','transport_rate_per_ton','transport_rate','transport_price','load_insurance_per_ton',
    'comms_due_per_ton','weight_ton_incoming','weight_ton_outgoing','is_transport_costs_inc_price','transport_cost','total_cost_price','additional_cost_1','additional_cost_2','additional_cost_3',
    'additional_cost_desc_1','additional_cost_desc_2','additional_cost_desc_3','gross_profit','gross_profit_percent',
    'gross_profit_per_ton','total_supplier_comm','total_customer_comm','total_comm','adjusted_gp','adjusted_gp_notes'*/

const emptyErrorsFinanceForm = computed(() => Object.keys(transport_finance_Form.errors).length === 0 && transport_finance_Form.errors.constructor === Object)

let transport_finance_Form = useForm({
    transport_rate_basis_id: props.transaction.transport_finance.transport_rate_basis_id,
    cost_price_per_unit: props.transaction.transport_finance.cost_price_per_unit,
    selling_price_per_unit: props.transaction.transport_finance.selling_price_per_unit,
    transport_rate:props.transaction.transport_finance.transport_rate,
    additional_cost_1: props.transaction.transport_finance.additional_cost_1,
    additional_cost_2: props.transaction.transport_finance.additional_cost_2,
    additional_cost_3: props.transaction.transport_finance.additional_cost_3,
    additional_cost_desc_1: props.transaction.transport_finance.additional_cost_desc_1,
    additional_cost_desc_2: props.transaction.transport_finance.additional_cost_desc_2,
    additional_cost_desc_3: props.transaction.transport_finance.additional_cost_desc_3,
    adjusted_gp: props.transaction.transport_finance.adjusted_gp,
    adjusted_gp_notes: props.transaction.transport_finance.adjusted_gp_notes,

});

const emptyErrorsInvoiceForm = computed(() => Object.keys(transport_invoice_Form.errors).length === 0 && transport_invoice_Form.errors.constructor === Object)


/*'old_id','transport_trans_id','type','comment','is_active','is_printed'*/
/*
'invoice_id','transport_trans_id','is_invoiced','is_invoice_paid','invoice_no', 'invoice_paid_date','invoice_pay_by_date','invoice_date','invoice_amount','invoice_amount_paid','cost_price','selling_price','status_id','notes'
*/


let transport_invoice_Form = useForm({
    transport_trans_id: props.transaction.id,
    old_id:props.transaction.transport_invoice.old_id,
    is_active:props.transaction.transport_invoice.is_active,
    is_printed:props.transaction.transport_invoice.is_printed,
    invoice_id:props.transaction.transport_invoice.transport_invoice_details.invoice_id,
    is_invoiced:props.transaction.transport_invoice.transport_invoice_details.is_invoiced,
    is_invoice_paid:props.transaction.transport_invoice.transport_invoice_details.is_invoice_paid,
    invoice_no:props.transaction.transport_invoice.transport_invoice_details.invoice_no,
    invoice_paid_date:props.transaction.transport_invoice.transport_invoice_details.invoice_paid_date,
    invoice_pay_by_date:props.transaction.transport_invoice.transport_invoice_details.invoice_pay_by_date,
    invoice_date:props.transaction.transport_invoice.transport_invoice_details.invoice_date,
    invoice_amount:props.transaction.transport_invoice.transport_invoice_details.invoice_amount,
    invoice_amount_paid:props.transaction.transport_invoice.transport_invoice_details.invoice_amount_paid,
    status_id:props.transaction.transport_invoice.transport_invoice_details.status_id,
    notes:props.transaction.transport_invoice.transport_invoice_details.notes
});



let status_Form = useForm({
    transport_trans_id: props.transaction.id,
    status_entity_id:1,
    status_type_id:1
});

/*'transport_trans_id','old_id','type','comment','is_active','is_printed','stamp_printed'*/

//['transport_trans_id','trade_rule_id','transport_job_id','approved_by_id','is_approved'];

let deal_ticket_Form = useForm({
    transport_trans_id: props.transaction.id,
    transport_job_id: props.transaction.transport_job.id,
    is_active: props.transaction.deal_ticket == null? false: props.transaction.deal_ticket.is_active,
    is_approved: props.transaction.deal_ticket == null? false: props.transaction.deal_ticket.is_approved,
    is_printed:props.transaction.deal_ticket == null? false: props.transaction.deal_ticket.is_printed,
});

let transport_approval_Form = useForm({
    transport_trans_id: props.transaction.id,
    transport_job_id: props.transaction.transport_job.id,
    deal_ticket_id: props.transaction.deal_ticket.id,

});


let NiceNumber = (_number) => {
    let val = (_number / 1).toFixed(2).replace(".", ".");
    return "R " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

const formatEarly = () => {
    const _date = new Date(transport_trans_Form.transport_date_earliest);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatLate = () => {
    const _date = new Date(transport_trans_Form.transport_date_latest);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoicePdDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_paid_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoicePayByDay = () => {
    const _date = new Date(transport_invoice_Form.invoice_pay_by_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatInvoiceDate = () => {
    const _date = new Date(transport_invoice_Form.invoice_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const enabled = ref(false)
const query = ref('')
const selectedPerson = ref(null)

const filteredPeople = computed(() =>
    query.value === ''
        ? people
        : people.filter((person) => {
            return person.name.toLowerCase().includes(query.value.toLowerCase())
        })
)

let contractTypeQuery = ref('');

const filteredContractTypes = computed(() =>
    contractTypeQuery.value === ''
        ? props.contract_types
        : props.contract_types.filter((type) => {
            return type.name.toLowerCase().includes(contractTypeQuery.value.toLowerCase())
        })
);


let productQuery = ref('');

const filteredProducts = computed(() =>
    productQuery.value === ''
        ? props.all_products
        : props.all_products.filter((product) => {
            return product.name.toLowerCase().includes(productQuery.value.toLowerCase())
        })
);

let supplierQuery = ref('');

const filteredSuppliers = computed(() =>
    supplierQuery.value === ''
        ? props.all_suppliers
        : props.all_suppliers.filter((supplier) => {
            return supplier.last_legal_name.toLowerCase().includes(supplierQuery.value.toLowerCase())
        })
);

let customerQuery = ref('');

const filteredCustomers = computed(() =>
    customerQuery.value === ''
        ? props.all_customers
        : props.all_customers.filter((customer) => {
            return customer.last_legal_name.toLowerCase().includes(customerQuery.value.toLowerCase())
        })
);

let transporterQuery = ref('');

const filteredTransporters = computed(() =>
    transporterQuery.value === ''
        ? props.all_transporters
        : props.all_transporters.filter((transporter) => {
            return transporter.last_legal_name.toLowerCase().includes(transporterQuery.value.toLowerCase())
        })
);

let confirmedTypeQuery = ref('');

const filteredConfirmationTypes = computed(() =>
    confirmedTypeQuery.value === ''
        ? props.confirmation_types
        : props.confirmation_types.filter((type) => {
            return type.name.toLowerCase().includes(confirmedTypeQuery.value.toLowerCase())
        })
);


const staffQuery = ref('');

let filteredStaff = computed(() =>
    staffQuery.value === ''
        ? props.all_staff
        : props.all_staff.filter((type) => {
            return type.first_name.toLowerCase().includes(staffQuery.value.toLowerCase())
        })
);

let productSourceQuery = ref('');

const filteredProductSources = computed(() =>
    productSourceQuery.value === ''
        ? props.all_product_sources
        : props.all_product_sources.filter((type) => {
            return type.name.toLowerCase().includes(productSourceQuery.value.toLowerCase())
        })
);


let packageIncomingQuery = ref('');

const filteredPackageIncoming = computed(() =>
    packageIncomingQuery.value === ''
        ? props.all_packaging
        : props.all_packaging.filter((type) => {
            return type.name.toLowerCase().includes(productSourceQuery.value.toLowerCase())
        })
);

let packageOutgoingQuery = ref('');

const filteredPackageOutgoing = computed(() =>
    packageOutgoingQuery.value === ''
        ? props.all_packaging
        : props.all_packaging.filter((type) => {
            return type.name.toLowerCase().includes(packageOutgoingQuery.value.toLowerCase())
        })
);

let billingUnitsIncomingQuery = ref('');

const filteredBillingUnitsIncoming = computed(() =>
    billingUnitsIncomingQuery.value === ''
        ? props.all_billing_units
        : props.all_billing_units.filter((type) => {
            return type.name.toLowerCase().includes(billingUnitsIncomingQuery.value.toLowerCase())
        })
);

let billingUnitsOutgoingQuery = ref('');

const filteredBillingUnitsOutgoing = computed(() =>
    billingUnitsOutgoingQuery.value === ''
        ? props.all_billing_units
        : props.all_billing_units.filter((type) => {
            return type.name.toLowerCase().includes(billingUnitsOutgoingQuery.value.toLowerCase())
        })
);


let collectionAddressQuery = ref('');

const filteredCollectionAddress = computed(() =>
    collectionAddressQuery.value === ''
        ? transport_trans_Form.supplier_id.addressable
        : transport_trans_Form.supplier_id.addressable.filter((address) => {
            return address.line_1.toLowerCase().includes(collectionAddressQuery.value.toLowerCase())
        })
);

let deliveryAddressQuery = ref('');

const filteredDeliveryAddress = computed(() =>
    deliveryAddressQuery.value === ''
        ? transport_trans_Form.customer_id.addressable
        : transport_trans_Form.customer_id.addressable.filter((address) => {
            return address.line_1.toLowerCase().includes(deliveryAddressQuery.value.toLowerCase())
        })
);

const permissions = computed(() => usePage().props.permissions);

//Form CRUD

const updateTransportTrans = () => {
    transport_trans_Form.put(route('transport_transaction.update', props.transaction.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
        }
    );
}

const updateTransportLoad = () => {
    transport_load_Form.put(route('transport_load.update', props.transaction.transport_load.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                //alert('Something went wrong')
                console.log(error)
            }
        }
    );
}


const updateTransportJob = () => {
    transport_job_Form.put(route('transport_job.update', props.transaction.transport_job.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}

let startTime = 0;
let endTime = 0;

const updateTransportFinance = () => {
    startTime = performance.now()
    transport_finance_Form.put(route('transport_finance.update', props.transaction.transport_finance.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                endTime = performance.now()
                console.log(`Call to transport Finance took ${(endTime - startTime)/1000} seconds`);
                startTime =0;
                endTime =0;
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}

const updateTransportInvoice = () => {
    transport_invoice_Form.put(route('transport_invoice.update', props.transaction.transport_invoice.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}

const createApproval = () => {
    transport_approval_Form.post(route('trans_approval.approve'), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

const createActivation = () => {
    transport_approval_Form.post(route('trans_approval.activate'), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};


const createStatus = () => {
    status_Form.post(route('transport_status.store'), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};


const updateDealTicket = () => {
    deal_ticket_Form.put(route('deal_ticket.update', props.transaction.deal_ticket.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                console.log(error)
            }
        }
    );
}


const temp_form = useForm({
});

const deleteAssignedComm =  (id) => {

    if (confirm("Sure you want to delete?")) {

        temp_form.delete(route('assigned_user_comm.destroy', id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    swal(usePage().props.jetstream.flash?.banner || '');
                },
                onError: (e) => {
                    console.log(e);
                },
            }
        );
    }
};

const deleteDriverVehicle = (id) => {

    if (confirm("Sure you want to delete?")) {

        temp_form.delete(route('transport_driver_vehicle.destroy', id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    swal(usePage().props.jetstream.flash?.banner || '');
                },
                onError: (e) => {
                    close();
                    console.log(e);
                },
            }
        );

        close();
    }

};


//Modals
let viewDriverVehicleModal = ref(false);
let viewDriverVehicleNewModal = ref(false);
let viewAssignedCommModal = ref(false);
let viewAssignedCommNewModal = ref(false);

let viewContractLinkModal = ref(false);


let currentDriverVehicle = ref(null);

let currentAssignedComm = ref(null);
//let currentDriverNewVehicle = ref(null);

const viewContractLink = () => {
    viewContractLinkModal.value = true;

};

const viewDriverVehicle = (driver_vehicle) => {
    currentDriverVehicle.value = driver_vehicle;
    viewDriverVehicleModal.value = true;
};

const viewDriverNewVehicle = () => {
    viewDriverVehicleNewModal.value = true;
};

const viewAssignedComm = (assigned_user_comm) => {

    currentAssignedComm.value = assigned_user_comm;
    viewAssignedCommModal.value = true;
};

const viewAssignedNewComm = () => {

    currentAssignedComm.value = null;
    viewAssignedCommNewModal.value = true;
};

const closeAssignedComm = () => {
    viewContractLinkModal.value = false;
};

const closeContractLink = () => {
    viewContractLinkModal.value = false;
};

const closeAssignedNewComm = () => {
    viewAssignedCommNewModal.value = false;
};

const closeDriverVehicleModal = () => {
    viewDriverVehicleModal.value = false;
    viewDriverVehicleNewModal.value = false;
};



const filteredLinkedContractsMq = computed(() =>
    props.linked_trans.filter((trans_link) => {
        return (trans_link.trans_link_type_id === 3);
    }
));

const filteredLinkedContractsPc = computed(() =>
    props.linked_trans.filter((trans_link) => {
            return (trans_link.trans_link_type_id === 3);
        }));

const sumLinkedContractsMq = computed(() => {

        let sum = 0;

        //transport_transaction.transport_finance.gross_profit

        if (props.linked_trans != null){

            for (let linked of props.linked_trans){

                if (linked.trans_link_type_id === 3){

                    if (linked.transport_transaction != null){
                        sum += linked.transport_transaction.transport_finance.gross_profit
                    }


                }
            }
        }
        return sum;
    });

const sumLinkedContractsPc = computed(() => {

    let sum = 0;

    //transport_transaction.transport_finance.gross_profit

    if (props.linked_trans != null){

        for (let linked of props.linked_trans){

            if (linked.trans_link_type_id === 3){
                if(linked.transport_transaction_pc != null){
                    sum += linked.transport_transaction_pc.transport_finance.gross_profit
                }
            }
        }
    }
    return sum;
});

const viewTrans = (id) => {
    alert(id)
    router.get('transport_transaction/'+id);
}

const roles_permissions = computed(() => usePage().props.roles_permissions)
const can_adjust_gp = computed(() => usePage().props.roles_permissions.permissions.includes("edit_adjusted_gp"))


</script>

<template>
    <AppLayout title="Transaction">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detailed Transaction / <span class="text-indigo-400">{{ transaction.id }}</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div
                        class="m-2 p-2 rounded-md rounded-md">
                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Transaction</div>
                            <div class=" text-indigo-400">{{ transaction.contract_type.name}}{{ transaction.id}}</div>
                            <div v-if="transaction.contract_type_id ===4" class="mb-2 text-gray-400">{{ transaction.contract_type.name}}{{ transaction.deal_ticket.old_id}}</div>
                            <div v-else class="mb-2 text-gray-400">{{ transaction.contract_type.name}}{{ transaction.old_id}}</div>


                            <form>
                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!--                                    'old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                                                                        'product_notes','supplier_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                                                                        'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done'-->

                                    <div class="text-right col-span-6 text-sm italic">
                                        (Last updated: {{ transaction.updated_at }})
                                    </div>
                                    <div class="flex col-span-6 mt-1">

                                        <div>
                                            <div>
                                                <VueDatePicker style="width: 250px;"
                                                               v-model="transport_trans_Form.transport_date_earliest"
                                                               :format="formatEarly"
                                                               :teleport="true"></VueDatePicker>
                                            </div>

                                            <div class="ml-3 text-sm font-bold">
                                                Transport date earliest
                                            </div>

                                            <InputError class="mt-2"
                                                        :message="transport_trans_Form.errors.transport_date_earliest"/>
                                        </div>

                                        <div class="ml-2">
                                            <div>
                                                <VueDatePicker style="width: 250px;"
                                                               v-model="transport_trans_Form.transport_date_latest"
                                                               :format="formatLate"
                                                               :teleport="true"></VueDatePicker>
                                            </div>

                                            <div class="ml-3 text-sm font-bold">
                                                Transport date latest
                                            </div>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.transport_date_latest"/>

                                    </div>
                                    <div class="flex col-span-4 mt-2">
                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_trans_Form.include_in_calculations"
                                                    :class="[transport_trans_Form.include_in_calculations ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_trans_Form.include_in_calculations ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Include in calculations</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_trans_Form.is_transaction_done"
                                                    :class="[transport_trans_Form.is_transaction_done ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_trans_Form.is_transaction_done ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Transaction done</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.is_transaction_done"/>
                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.include_in_calculations"/>

                                    </div>
                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_trans_Form.contract_type_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Contract
                                                    Type:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="contractTypeQuery = $event.target.value"
                                                        :display-value="(type) => type?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredContractTypes.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="type in filteredContractTypes"
                                                                        :key="type.id" :value="type" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ type.name }}
                                                                </span>
                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_trans_Form.errors.contract_type_id"/>

                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_trans_Form.product_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Product:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="productQuery = $event.target.value"
                                                        :display-value="(product) => product?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredProducts.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="product in filteredProducts"
                                                                        :key="product" :value="product" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ product.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2" :message="transport_trans_Form.errors.product_id"/>
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_trans_Form.supplier_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Supplier:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="supplierQuery = $event.target.value"
                                                        :display-value="(supplier) => supplier?.last_legal_name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredSuppliers.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="supplier in filteredSuppliers"
                                                                        :key="supplier.id" :value="supplier"
                                                                        as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ supplier.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_trans_Form.errors.supplier_id"/>
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_trans_Form.customer_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Customer:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="customerQuery = $event.target.value"
                                                        :display-value="(customer) => customer?.last_legal_name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredCustomers.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="customer in filteredCustomers"
                                                                        :key="customer.id" :value="customer"
                                                                        as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ customer.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_trans_Form.errors.customer_id"/>
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_trans_Form.transporter_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">
                                                    Transporter:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="transporterQuery = $event.target.value"
                                                        :display-value="(transporter) => transporter?.last_legal_name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredTransporters.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="transporter in filteredTransporters"
                                                                        :key="transporter.id" :value="transporter"
                                                                        as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ transporter.last_legal_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_trans_Form.errors.transporter_id"/>
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">delivery_notes:</label>
                                        <AreaInput
                                            id="delivery_notes"
                                            v-model="transport_trans_Form.delivery_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.delivery_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">product_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.product_notes"
                                            id="product_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.product_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">suppliers_notes:</label>
                                        <AreaInput
                                            id="supplier_notes"
                                            v-model="transport_trans_Form.suppliers_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.suppliers_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">customer_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.customer_notes"
                                            id="customer_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.customer_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">traders_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.traders_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.traders_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">transport_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.transport_notes"
                                            id="transport_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.transport_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">pricing_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.pricing_notes"
                                            id="pricing_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.pricing_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">process_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.process_notes"
                                            id="process_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.process_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">document_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.document_notes"
                                            id="document_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2" :message="transport_trans_Form.errors.document_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">transaction_notes:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.transaction_notes"
                                            id="transaction_notes"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />
                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.transaction_notes"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">traders_notes_supplier:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.traders_notes_supplier"
                                            id="traders_notes_supplier"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.traders_notes_supplier"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">traders_notes_customer:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.traders_notes_customer"
                                            id="traders_notes_customer"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.traders_notes_customer"/>
                                    </div>
                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">traders_notes_transport:</label>
                                        <AreaInput
                                            v-model="transport_trans_Form.traders_notes_transport"
                                            id="traders_notes_transport"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.traders_notes_transport"/>
                                    </div>
                                    <div class="col-span-4">

                                        <SecondaryButton class="m-1" @click="updateTransportTrans">
                                            Update
                                        </SecondaryButton>

                                        <SecondaryButton class="m-1">
                                            Delete
                                        </SecondaryButton>
                                    </div>


                                </div>
                            </form>
                        </div>

                    </div>
                </div>


                <div v-if="transaction.contract_type_id === 4">
                    <SectionBorder/>
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                        <div class="m-2 p-2">

                            <div class="">
                                <div class="text-lg mb-2 text-indigo-400">Deal Ticket</div>

                                <div v-if="transaction.deal_ticket.is_active" class=" mt-3">
                                    <div class="text-green-400">Deal Ticket is Active </div>
                                    <div class=" text-indigo-400">{{ transaction.contract_type.name}}{{ transaction.id}}</div>
                                    <div v-if="transaction.contract_type_id ===4" class="mb-2 text-gray-400">{{ transaction.contract_type.name}}{{ transaction.deal_ticket.old_id}}</div>
                                    <div v-else class="mb-2 text-gray-400">{{ transaction.contract_type.name}}{{ transaction.old_id}}</div>
                                </div>

                                <div v-else class="text-red-400 mt-3">
                                    Deal Ticket Not Active
                                </div>

                                <form class="mt-5">

                                    <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                        <div class="flex col-span-4 mt-2">
                                            <SwitchGroup as="div" class="flex m-2 items-center">
                                                <Switch v-model="deal_ticket_Form.is_active"
                                                        :class="[deal_ticket_Form.is_active ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[deal_ticket_Form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                </Switch>
                                                <SwitchLabel as="span" class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900">Deal ticket active</span>
                                                </SwitchLabel>
                                            </SwitchGroup>

                                            <SwitchGroup as="div" class="flex m-2 items-center">
                                                <Switch v-model="deal_ticket_Form.is_printed"
                                                        :class="[deal_ticket_Form.is_printed ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[deal_ticket_Form.is_printed ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                </Switch>
                                                <SwitchLabel as="span" class="ml-3 text-sm">
                                                    <span class="font-medium text-gray-900">Deal ticket printed</span>
                                                </SwitchLabel>
                                            </SwitchGroup>


                                        </div>

                                        <div class="col-span-4 mt-2">

                                            <div v-if="transaction.deal_ticket.is_approved"  class="flex-row text-green-400 text-lg">Trade approved (can activate)</div>
                                            <div v-else class="flex-row text-indigo-400 text-lg">Requires approvals</div>

                                            <SecondaryButton class="m-1 mt-2" @click="createApproval">
                                                Approve
                                            </SecondaryButton>

                                            <SecondaryButton class="m-1 mt-2" @click="createActivation">
                                                Activate
                                            </SecondaryButton>
                                        </div>

                                        <div class="col-span-4 mt-2">
                                            <div class="shadow">

                                                <div class="px-4 sm:px-6 lg:px-8">
                                                    <div class="flex-row text-indigo-400 text-lg mb-2">
                                                        <span>Trade Rules </span>
                                                    </div>

                                                    <div class="sm:flex sm:items-center">
                                                        <div class="sm:flex-auto">
                                                            <h1 class="text-base font-semibold leading-6 text-gray-900">Approvals:</h1>
                                                            <p class="mt-2 text-sm text-gray-700">Approvals based on the applied trading rule.</p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-8 flow-root">
                                                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                                                <table class="min-w-full divide-y divide-gray-300">
                                                                    <thead>
                                                                    <tr>
                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Rule</th>
                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Required Role</th>
                                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Approved details</th>

                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="divide-y divide-gray-200">
                                                                    <tr v-for="(n,index) in rules_with_approvals.TradeRule" :key="index">

                                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                                            {{n.rule}}</td>

                                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                                            {{ n.role}}</td>

                                                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">

                                                                            <ul>
                                                                                <li v-if="n.approvals.length > 0" v-for="(m,index) in n.approvals" :key="index">
                                                                                    <div class="flex">
                                                                                        <span><check-icon class="w-6 h-6 fill-green-300 mr-3" />  </span> <span> {{m.user.name}} ({{m.user.created_at}})</span>
                                                                                    </div>
                                                                                </li>

                                                                                <div v-else class="flex">

                                                                                    <span><XCircleIcon class="w-6 h-6 fill-red-400 mr-3" />  </span> <span>None received..</span>
                                                                                </div>
                                                                            </ul>

                                                                        </td>

                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-span-4 mt-2">
                                            <div class="shadow">

                                                <div class="px-4 sm:px-6 lg:px-8">
                                                    <div class="flex-row text-indigo-400 text-lg mb-2">
                                                        <span>Trade Operation Rules </span>
                                                    </div>

                                                    <div class="sm:flex sm:items-center">
                                                        <div class="sm:flex-auto">
                                                            <h1 class="text-base font-semibold leading-6 text-gray-900">Approvals:</h1>
                                                            <p class="mt-2 text-sm text-gray-700">Approvals based on the applied trading operation rule.</p>
                                                        </div>
                                                    </div>
                                                    <div class="mt-8 flow-root">
                                                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                                                <table class="min-w-full divide-y divide-gray-300">
                                                                    <thead>
                                                                    <tr>
                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Rule</th>
                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Required Role</th>
                                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Approved details</th>

                                                                    </tr>
                                                                    </thead>
                                                                    <tbody class="divide-y divide-gray-200">


                                                                    <tr v-for="(n,index) in rules_with_approvals.TradeRuleOpp" :key="index">

                                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                                            {{n.rule}}</td>

                                                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                                            {{ n.role}}</td>

                                                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">

                                                                            <ul>
                                                                                <li v-if="n.approvals.length > 0" v-for="(m,index) in n.approvals" :key="index">

                                                                                    <div class="flex">
                                                                                        <span><check-icon class="w-6 h-6 fill-green-300 mr-3" />  </span> <span> {{m.user.name}} ({{m.user.created_at}})</span>
                                                                                    </div>
                                                                                </li>

                                                                                <div v-else class="flex">

                                                                                    <span><XCircleIcon class="w-6 h-6 fill-red-400 mr-3" />  </span> <span>None received..</span>
                                                                                </div>
                                                                            </ul>

                                                                        </td>

                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>


                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>
                </div>


                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Linked Contracts</div>

                            <div class="">

                            <div v-if="transaction.contract_type_id === 1">
                                Unallocated
                            </div>

                            <div v-if="transaction.contract_type_id === 2">

                                <div class="text-indigo-400 font-bold">
                                    PC (MQ's linked to this contract)
                                </div>
                                <div>
                                    <form class="mt-5">

                                        <div class="px-4 sm:px-6 lg:px-8">
                                            <div class="-mx-4 mt-8 flow-root sm:mx-0">
                                                <table class="min-w-full">
                                                    <colgroup>
                                                        <col class="w-full sm:w-1/2" />
                                                        <col class="sm:w-1/6" />
                                                        <col class="sm:w-1/6" />
                                                        <col class="sm:w-1/6" />
                                                    </colgroup>
                                                    <thead class="border-b border-gray-300 text-gray-900">
                                                    <tr>
                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Parties</th>
                                                        <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">Product</th>
                                                        <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">GP</th>
                                                        <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="contract in filteredLinkedContractsMq" :key="contract.id" class="border-b border-gray-200">


                                                        <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                            <div class="font-medium text-gray-900">{{contract.transport_transaction.supplier.last_legal_name}}</div>
                                                            <div class="mt-1 truncate text-gray-500">{{contract.transport_transaction.customer.last_legal_name}}</div>
                                                            <div class="mt-1 truncate text-gray-500">{{contract.transport_transaction.transporter.last_legal_name}}</div>
                                                        </td>
                                                        <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{contract.transport_transaction.product.name}}</td>
                                                        <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{ NiceNumber(contract.transport_transaction.transport_finance.gross_profit)}}</td>
                                                        <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                                            <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',contract.transport_transaction.id)" >View trans</Link>
                                                        </td>


                                                    </tr>
                                                    </tbody>
                                                    <tfoot>

                                                    <tr>
                                                        <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-semibold text-gray-900 sm:table-cell sm:pl-0">Total GP</th>
                                                        <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">{{NiceNumber(sumLinkedContractsMq)}}</td>
                                                        <th scope="row" class="pl-6 pr-3 pt-4 text-left text-sm font-semibold text-gray-900 sm:hidden">Total</th>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>

                                    </form>
                                </div>

                            </div>

                            <div v-if="transaction.contract_type_id === 3">
                                SC
                            </div>

                            <div v-if="transaction.contract_type_id === 4">

                                <div class="text-indigo-400 font-bold">
                                    MQ
                                </div>

                                <SecondaryButton class="m-1 mt-3" @click="viewContractLink">
                                    Link MQ
                                </SecondaryButton>

                                <ContractLinkModal
                                    :show="viewContractLinkModal"
                                    @close="closeContractLink"
                                    :mq_trans_id="transaction.id"
                                    :link_type_id="3"
                                />

                                <div class="mt-3">

                                    <div>PC linked to this MQ:</div>

                                    <div>
                                        <form class="mt-5">

                                            <div class="px-4 sm:px-6 lg:px-8">
                                                <div class="-mx-4 mt-8 flow-root sm:mx-0">
                                                    <table class="min-w-full">
                                                        <colgroup>
                                                            <col class="w-full sm:w-1/2" />
                                                            <col class="sm:w-1/6" />
                                                            <col class="sm:w-1/6" />
                                                            <col class="sm:w-1/6" />
                                                        </colgroup>
                                                        <thead class="border-b border-gray-300 text-gray-900">
                                                        <tr>
                                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Parties</th>
                                                            <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">Product</th>
                                                            <th scope="col" class="hidden px-3 py-3.5 text-right text-sm font-semibold text-gray-900 sm:table-cell">GP</th>
                                                            <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">Action</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="contract in filteredLinkedContractsPc" :key="contract.id" class="border-b border-gray-200">

                                                            <td class="max-w-0 py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                                <div class="font-medium text-gray-900">{{contract.transport_transaction_pc.supplier.last_legal_name}}</div>
                                                                <div class="mt-1 truncate text-gray-500">{{contract.transport_transaction_pc.customer.last_legal_name}}</div>
                                                                <div class="mt-1 truncate text-gray-500">{{contract.transport_transaction_pc.transporter.last_legal_name}}</div>
                                                            </td>
                                                            <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{contract.transport_transaction_pc.product.name}}</td>
                                                            <td class="hidden px-3 py-5 text-right text-sm text-gray-500 sm:table-cell">{{ NiceNumber(contract.transport_transaction_pc.transport_finance.gross_profit)}}</td>
                                                            <td class="py-5 pl-3 pr-4 text-right text-sm text-gray-500 sm:pr-0">
                                                                <Link class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" :href="route('transport_transaction.show',contract.transport_transaction_pc.id)" >View trans</Link>
                                                            </td>


                                                        </tr>
                                                        </tbody>
                                                        <tfoot>

                                                        <tr>
                                                            <th scope="row" colspan="3" class="hidden pl-4 pr-3 pt-4 text-right text-sm font-semibold text-gray-900 sm:table-cell sm:pl-0">Total GP</th>
                                                            <td class="pl-3 pr-4 pt-4 text-right text-sm font-semibold text-gray-900 sm:pr-0">{{NiceNumber(sumLinkedContractsPc)}}</td>
                                                            <th scope="row" class="pl-6 pr-3 pt-4 text-left text-sm font-semibold text-gray-900 sm:hidden">Total</th>
                                                        </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                        </form>
                                    </div>

                                </div>



                            </div>

                            </div>


                        </div>

                    </div>

                </div>
                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div :class="emptyErrorsLoadForm ?'m-2 p-2':'m-2 p-2 border border-solid border-red-500 rounded'">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Load</div>


                            <!--                            'transport_trans_id','confirmed_by_id','confirmed_by_type_id',
                                                        'product_id','packaging_incoming_id','packaging_outgoing_id','product_source_id','product_grade_perc','no_units_incoming'
                                                        ,'billing_units_incoming_id','no_units_outgoing','billing_units_outgoing_id','is_weighbridge_certificate_received'
                                                        ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id'-->
                            <form>
                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <!--                                    'transport_trans_id','confirmed_by_id','confirmed_by_type_id',
                                                                        'product_id','packaging_incoming_id','packaging_outgoing_id','product_source_id','product_grade_perc','no_units_incoming'
                                                                        ,'billing_units_incoming_id','no_units_outgoing','billing_units_outgoing_id','is_weighbridge_certificate_received'

                                                                        ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id'-->

                                    <div class="col-span-4">
                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_load_Form.is_weighbridge_certificate_received"
                                                    :class="[transport_load_Form.is_weighbridge_certificate_received ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_load_Form.is_weighbridge_certificate_received ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span
                                                    class="font-medium text-gray-900">Weighbridge certificate received</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <InputError class="mt-2"
                                                    :message="transport_load_Form.errors.is_weighbridge_certificate_received"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">No units
                                            incoming:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_load_Form.no_units_incoming" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_load_Form.errors.no_units_incoming"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">No units
                                            outgoing:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_load_Form.no_units_outgoing" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_load_Form.errors.no_units_outgoing"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">Product
                                            grade:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_load_Form.product_grade_perc" type="text"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_load_Form.errors.product_grade_perc"/>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.confirmed_by_type_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Confirmed
                                                    via:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="confirmedTypeQuery = $event.target.value"
                                                        :display-value="(type) => type?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredConfirmationTypes.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="type in filteredConfirmationTypes"
                                                                        :key="type.id" :value="type" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ type.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['confirmed_by_type_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.confirmed_by_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Confirmed
                                                    by:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="filteredStaff = $event.target.value"
                                                        :display-value="(staff) => staff?.first_name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredStaff.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="staff in filteredStaff" :key="staff.id"
                                                                        :value="staff" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ staff.first_name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['confirmed_by_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.product_source_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Product
                                                    source:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="productSourceQuery = $event.target.value"
                                                        :display-value="(source) => source?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredProductSources.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="source in filteredProductSources"
                                                                        :key="source.id" :value="source" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ source.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['product_source_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.packaging_incoming_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Package
                                                    incoming:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="packageIncomingQuery = $event.target.value"
                                                        :display-value="(packaging) => packaging?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredPackageIncoming.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="packaging in filteredPackageIncoming"
                                                                        :key="packaging.id" :value="packaging"
                                                                        as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ packaging.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['packaging_incoming_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.packaging_outgoing_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Package
                                                    outgoing:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="packageOutgoingQuery = $event.target.value"
                                                        :display-value="(packaging) => packaging?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredPackageOutgoing.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="packaging in filteredPackageOutgoing"
                                                                        :key="packaging.id" :value="packaging"
                                                                        as="template" v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ packaging.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['packaging_outgoing_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.billing_units_incoming_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Billing
                                                    units incoming:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="billingUnitsIncomingQuery = $event.target.value"
                                                        :display-value="(units) => units?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredBillingUnitsIncoming.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="units in filteredBillingUnitsIncoming"
                                                                        :key="units.id" :value="units" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ units.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['billing_units_incoming_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.billing_units_outgoing_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Billing
                                                    units outgoing:
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="billingUnitsOutgoingQuery = $event.target.value"
                                                        :display-value="(units) => units?.name"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredBillingUnitsOutgoing.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="units in filteredBillingUnitsOutgoing"
                                                                        :key="units.id" :value="units" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <span
                                                                    :class="['block truncate', selected && 'font-semibold']">
                                                                  {{ units.name }}
                                                                </span>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                  <CheckIcon class="h-5 w-5" aria-hidden="true"/>
                                                                </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['billing_units_outgoing_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.collection_address_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Collection
                                                    address (supplier):
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="collectionAddressQuery = $event.target.value"
                                                        :display-value="(address) => address?.line_1"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredCollectionAddress.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="address in filteredCollectionAddress"
                                                                        :key="address.id" :value="address" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <div class="flex items-center">
                                                                    <span
                                                                        :class="['inline-block h-2 w-2 flex-shrink-0 rounded-full', address.is_primary ? 'bg-green-400' : 'bg-gray-200']"
                                                                        aria-hidden="true"/>
                                                                    <span
                                                                        :class="['ml-3 truncate', selected && 'font-semibold']">
                                                                            <span>
                                                                                {{ address.line_1 }}
                                                                            </span>
                                                                            <span v-if="address.line_2">
                                                                               , {{ address.line_2 }}
                                                                            </span>
                                                                            <span v-if="address.line_3">
                                                                               , {{ address.line_3 }}
                                                                            </span>
                                                                     <span class="sr-only"> is {{
                                                                             address.is_primary ? 'online' : 'offline'
                                                                         }}</span> </span>
                                                                </div>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                    <CheckIcon class="h-5 w-5"
                                                                               aria-hidden="true"/> </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <SecondaryButton class="m-1">
                                                View supplier
                                            </SecondaryButton>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['collection_address_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div v-if="transport_load_Form.collection_address_id">
                                            <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                <div class="px-4 sm:px-0">
                                                    <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                        Selected Collection Address:</h3>
                                                    <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                                                        Supplier address details:</p>
                                                </div>
                                                <div class="mt-6 border-t border-gray-100">
                                                    <dl class="divide-y divide-gray-100">
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                1
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.line_1 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                2
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.line_2 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                3
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.line_3 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Country
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.country }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Code
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.code }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Long
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{
                                                                    transport_load_Form.collection_address_id.longitude
                                                                }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Lat
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.collection_address_id.latitude }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Directions
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{
                                                                    transport_load_Form.collection_address_id.directions
                                                                }}
                                                            </dd>
                                                        </div>

                                                    </dl>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else>
                                            No supplier addresses loaded...
                                        </div>

                                    </div>

                                    <div class="col-span-4">
                                        <div class="mt-2">

                                            <Combobox as="div" v-model="transport_load_Form.delivery_address_id">
                                                <ComboboxLabel
                                                    class="block text-sm font-medium leading-6 text-gray-900">Delivery
                                                    address (customer):
                                                </ComboboxLabel>
                                                <div class="relative mt-2">
                                                    <ComboboxInput
                                                        class="w-full rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                                        @change="deliveryAddressQuery = $event.target.value"
                                                        :display-value="(address) => address?.line_1"/>
                                                    <ComboboxButton
                                                        class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                                                        <ChevronUpDownIcon class="h-5 w-5 text-gray-400"
                                                                           aria-hidden="true"/>
                                                    </ComboboxButton>

                                                    <ComboboxOptions v-if="filteredDeliveryAddress.length > 0"
                                                                     class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                                                        <ComboboxOption v-for="address in filteredDeliveryAddress"
                                                                        :key="address.id" :value="address" as="template"
                                                                        v-slot="{ active, selected }">
                                                            <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                <div class="flex items-center">
                                                                    <span
                                                                        :class="['inline-block h-2 w-2 flex-shrink-0 rounded-full', address.is_primary ? 'bg-green-400' : 'bg-gray-200']"
                                                                        aria-hidden="true"/>
                                                                    <span
                                                                        :class="['ml-3 truncate', selected && 'font-semibold']">
                                                                            <span>
                                                                                {{ address.line_1 }}
                                                                            </span>
                                                                            <span v-if="address.line_2">
                                                                               , {{ address.line_2 }}
                                                                            </span>
                                                                            <span v-if="address.line_3">
                                                                               , {{ address.line_3 }}
                                                                            </span>
                                                                     <span class="sr-only"> is {{
                                                                             address.is_primary ? 'online' : 'offline'
                                                                         }}</span> </span>
                                                                </div>

                                                                <span v-if="selected"
                                                                      :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                    <CheckIcon class="h-5 w-5"
                                                                               aria-hidden="true"/> </span>
                                                            </li>
                                                        </ComboboxOption>
                                                    </ComboboxOptions>
                                                </div>
                                            </Combobox>

                                            <SecondaryButton class="m-1">
                                                View supplier
                                            </SecondaryButton>

                                            <InputError class="mt-2"
                                                        :message="transport_load_Form.errors['delivery_address_id.id']"/>
                                        </div>
                                    </div>

                                    <div class="col-span-4">
                                        <div v-if="transport_load_Form.delivery_address_id">
                                            <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                <div class="px-4 sm:px-0">
                                                    <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                        Selected Delivery Address:</h3>
                                                    <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                                                        Customer address details:</p>
                                                </div>
                                                <div class="mt-6 border-t border-gray-100">
                                                    <dl class="divide-y divide-gray-100">
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                1
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.line_1 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                2
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.line_2 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Line
                                                                3
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.line_3 }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Country
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.country }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Code
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.code }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Long
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.longitude }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Lat
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.latitude }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Directions
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ transport_load_Form.delivery_address_id.directions }}
                                                            </dd>
                                                        </div>

                                                    </dl>
                                                </div>
                                            </div>
                                        </div>

                                        <div v-else>
                                            No supplier addresses loaded...
                                        </div>

                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton class="m-1" @click="updateTransportLoad">
                                            Update
                                        </SecondaryButton>

                                        <SecondaryButton class="m-1">
                                            Delete
                                        </SecondaryButton>
                                    </div>

                                </div>
                            </form>

                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div :class="emptyErrorsJobForm ?'m-2 p-2':'m-2 p-2 border border-solid border-red-500 rounded'">

                        <!--                        'transport_trans_id','transport_rate_basis_id','customer_order_number','is_multi_loads',
                                                'is_approved','transport_date_earliest','transport_date_latest','is_transport_costs_inc_price','is_product_zero_rated',
                                                'loading_hours_from_id','loading_hours_to_id','offloading_hours_from_id','offloading_hours_to_id','load_insurance_per_ton',
                                                'total_load_insurance','number_loads','loading_instructions','offloading_instructions','comments'-->
                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Job</div>

                            <form class="mt-5">

                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">customer_order_number:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_job_Form.customer_order_number" type="text"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_job_Form.errors.customer_order_number"/>

                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">number_loads:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_job_Form.number_loads" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_job_Form.errors.number_loads"/>

                                    </div>

                                    <div class="flex col-span-4 mt-2">
                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_job_Form.is_multi_loads"
                                                    :class="[transport_job_Form.is_multi_loads ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_job_Form.is_multi_loads ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Multi loads</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_job_Form.is_approved"
                                                    :class="[transport_job_Form.is_approved ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_job_Form.is_approved ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Approved</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_job_Form.is_transport_costs_inc_price"
                                                    :class="[transport_job_Form.is_transport_costs_inc_price ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_job_Form.is_transport_costs_inc_price ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Transport cost in price</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_job_Form.is_product_zero_rated"
                                                    :class="[transport_job_Form.is_product_zero_rated ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_job_Form.is_product_zero_rated ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Product zero rated</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <InputError class="mt-2"
                                                    :message="transport_trans_Form.errors.is_transaction_done"/>


                                    </div>

                                    <div class="mt-2 flex col-span-4">

                                        <div class="col">

                                            <label class="block text-sm font-medium leading-6 text-gray-900">Loading
                                                hour from:</label>

                                            <select v-model="transport_job_Form.loading_hours_from_id"
                                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option v-for="n in props.loading_hour_options" :key="n.id"
                                                        :value="n.id">{{
                                                        n.name
                                                    }}
                                                </option>
                                            </select>

                                            <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->

                                        </div>

                                        <div class="col ml-2">

                                            <label class="block text-sm font-medium leading-6 text-gray-900">Loading
                                                hour to:</label>

                                            <select v-model="transport_job_Form.loading_hours_to_id"
                                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option v-for="n in props.loading_hour_options" :key="n.id"
                                                        :value="n.id">
                                                    {{
                                                        n.name
                                                    }}
                                                </option>
                                            </select>

                                            <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->

                                        </div>

                                        <div class="col ml-2">

                                            <label class="block text-sm font-medium leading-6 text-gray-900">Offloading
                                                hour to:</label>

                                            <select v-model="transport_job_Form.offloading_hours_from_id"
                                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option v-for="n in props.loading_hour_options" :key="n.id"
                                                        :value="n.id">
                                                    {{
                                                        n.name
                                                    }}
                                                </option>
                                            </select>

                                            <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->

                                        </div>

                                        <div class="col ml-2">

                                            <label class="block text-sm font-medium leading-6 text-gray-900">Offloading
                                                hour to:</label>

                                            <select v-model="transport_job_Form.offloading_hours_to_id"
                                                    class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                <option v-for="n in props.loading_hour_options" :key="n.id"
                                                        :value="n.id">
                                                    {{
                                                        n.name
                                                    }}
                                                </option>
                                            </select>

                                            <!--                                        <InputError class="mt-2" :message="customerForm.errors.invoice_basis_id"/>-->

                                        </div>

                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">loading_instructions:</label>
                                        <AreaInput
                                            v-model="transport_job_Form.loading_instructions"
                                            id="loading_instructions"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_job_Form.errors.loading_instructions"/>
                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">offloading_instructions:</label>
                                        <AreaInput
                                            v-model="transport_job_Form.offloading_instructions"
                                            id="offloading_instructions"
                                            :rows=6
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_job_Form.errors.offloading_instructions"/>

                                    </div>

                                    <div class="col-span-4 mt-2">

                                        <div class="text-lg ml-6 text-indigo-400">Driver vehicles:</div>

                                        <div v-if="viewDriverVehicleNewModal">

                                            <driver-vehicle-modal-add
                                                :show="viewDriverVehicleNewModal" @close="closeDriverVehicleModal"
                                                :transport_trans_id="props.transaction.id"
                                                :transport_job_id="props.transaction.transport_job.id"
                                                :driver_vehicle="null"
                                                :all_drivers="props.all_drivers"
                                                :all_vehicles="props.all_vehicles"
                                            />

                                        </div>

                                        <SecondaryButton @click="viewDriverNewVehicle()" class="m-6">
                                            Add Driver Vehicle (+)
                                        </SecondaryButton>

                                    </div>

                                    <div class="col-span-4">
                                        <div
                                            v-for="(driver_vehicle,index) in transaction.transport_job.transport_driver_vehicle"
                                            :key="driver_vehicle.id">
                                            <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                <div class="px-4 sm:px-0">
                                                    <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                                                        Driver vehicle {{ index + 1 }}</h3>
                                                    <h3 class="text-base font-semibold leading-7 text-sm text-gray-400">
                                                        Reference {{ driver_vehicle.id }}</h3>
                                                </div>
                                                <div class="mt-6 border-t border-gray-100">
                                                    <dl class="divide-y divide-gray-100">
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Weighbridge upload weight
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ driver_vehicle.weighbridge_upload_weight }}
                                                            </dd>
                                                        </div>


                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Weighbridge offload weight
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ driver_vehicle.weighbridge_offload_weight }}
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Weighbridge variance
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                <div v-if="driver_vehicle.is_weighbridge_variance">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Cancelled
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                <div class="flex" v-if="driver_vehicle.is_cancelled">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_cancelled && driver_vehicle.date_cancelled ? '(' + driver_vehicle.date_cancelled + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_cancelled && driver_vehicle.date_cancelled ? '(' + driver_vehicle.date_cancelled + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>

                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Loaded
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                <div class="flex" v-if="driver_vehicle.is_loaded">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">On
                                                                Road
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                <div class="flex" v-if="driver_vehicle.is_onroad">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_onroad && driver_vehicle.date_onroad ? '(' + driver_vehicle.date_onroad + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_onroad && driver_vehicle.date_onroad ? '(' + driver_vehicle.date_onroad + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Delivered
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                <div class="flex" v-if="driver_vehicle.is_delivered">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_delivered && driver_vehicle.date_delivered ? '(' + driver_vehicle.date_delivered + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_delivered && driver_vehicle.date_delivered ? '(' + driver_vehicle.date_delivered + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Scheduled
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                <div class="flex"
                                                                     v-if="driver_vehicle.is_transport_scheduled">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">Paid
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                <div class="flex" v-if="driver_vehicle.is_paid">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_paid && driver_vehicle.date_paid ? '(' + driver_vehicle.date_paid + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_paid && driver_vehicle.date_paid ? '(' + driver_vehicle.date_paid + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Payment Overdue
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                <div class="flex"
                                                                     v-if="driver_vehicle.is_payment_overdue">
                                                                    <icon name="tick-circle"
                                                                          class="mr-2 w-6 h-6 fill-green-200"/>
                                                                    {{
                                                                        driver_vehicle.is_payment_overdue && driver_vehicle.date_payment_overdue ? '(' + driver_vehicle.date_payment_overdue + ')' : ''
                                                                    }}
                                                                </div>
                                                                <div v-else>
                                                                    <icon name="cross-solid"
                                                                          class="mr-2 w-6 h-6 fill-red-500"/>
                                                                    {{
                                                                        driver_vehicle.is_payment_overdue && driver_vehicle.date_payment_overdue ? '(' + driver_vehicle.date_payment_overdue + ')' : ''
                                                                    }}
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Traders Notes
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ driver_vehicle.traders_notes }}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                Operation alert Notes
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ driver_vehicle.operations_alert_notes }}
                                                            </dd>
                                                        </div>

                                                        <div>
                                                            <div class="mt-3">
                                                                <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                                    <div class="px-4 sm:px-0">
                                                                        <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                            Selected Driver</h3>
                                                                    </div>

                                                                    <div class="mt-6 border-t border-gray-100">
                                                                        <dl class="divide-y divide-gray-100">
                                                                            <div
                                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                                    First name
                                                                                </dt>
                                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                                    {{ driver_vehicle.driver.first_name }}
                                                                                </dd>
                                                                            </div>

                                                                            <div
                                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                                    Last name
                                                                                </dt>
                                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                                    {{ driver_vehicle.driver.last_name }}
                                                                                </dd>
                                                                            </div>

                                                                            <div
                                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                                    Cell no
                                                                                </dt>
                                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                                    {{ driver_vehicle.driver.cell_no }}
                                                                                </dd>
                                                                            </div>


                                                                        </dl>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div>
                                                            <div class="mt-3">
                                                                <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                                    <div class="px-4 sm:px-0">
                                                                        <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                            Selected Vehicle</h3>
                                                                    </div>

                                                                    <div class="mt-6 border-t border-gray-100">
                                                                        <dl class="divide-y divide-gray-100">
                                                                            <div
                                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                                    Registration number
                                                                                </dt>
                                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                                    {{ driver_vehicle.vehicle.reg_no }}
                                                                                </dd>
                                                                            </div>

                                                                            <div
                                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                                    Vehicle Type
                                                                                </dt>
                                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">

                                                                                    {{ driver_vehicle.vehicle.vehicle_type.name }}
                                                                                </dd>
                                                                            </div>


                                                                        </dl>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </dl>
                                                </div>

                                                <div class="mt-6 col-span-4">

                                                    <div v-if="viewDriverVehicleModal">
                                                        <DriverVehicleModal :show="viewDriverVehicleModal"
                                                                            @close="closeDriverVehicleModal"
                                                                            :transport_trans_id="props.transaction.id"
                                                                            :transport_job_id="props.transaction.transport_job.id"
                                                                            :driver_vehicle="currentDriverVehicle"
                                                                            :all_drivers="props.all_drivers"
                                                                            :all_vehicles="props.all_vehicles"
                                                        />
                                                    </div>


                                                    <SecondaryButton class="m-1"
                                                                     @click="viewDriverVehicle(driver_vehicle)">
                                                        Edit
                                                    </SecondaryButton>

                                                    <SecondaryButton class="m-1"
                                                                     @click="deleteDriverVehicle(driver_vehicle.id)">
                                                        Delete
                                                    </SecondaryButton>


                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton @click="updateTransportJob" class="m-1">
                                            Update
                                        </SecondaryButton>
                                    </div>

                                </div>

                            </form>


                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div :class="emptyErrorsFinanceForm ?'m-2 p-2':'m-2 p-2 border border-solid border-red-500 rounded'">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Finance</div>

                            <!--                            'transport_trans_id','transport_load_id','transport_rate_basis_id','cost_price_per_unit','cost_price_per_ton','selling_price_per_unit','cost_price',
                                                        'selling_price','selling_price_per_ton','cost_price_per_unit','selling_price_per_unit','transport_rate_per_ton','transport_rate','transport_price','load_insurance_per_ton',
                                                        'comms_due_per_ton','weight_ton_incoming','weight_ton_outgoing','is_transport_costs_inc_price','transport_cost','total_cost_price','additional_cost_1','additional_cost_2','additional_cost_3',
                                                        'additional_cost_desc_1','additional_cost_desc_2','additional_cost_desc_3','gross_profit','gross_profit_percent',
                                                        'gross_profit_per_ton','total_supplier_comm','total_customer_comm','total_comm','adjusted_gp','adjusted_gp_notes'-->


                            <form class="mt-5">

                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">cost_price_per_unit ({{transport_load_Form.billing_units_incoming_id.name}}) incoming:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_finance_Form.cost_price_per_unit" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                            <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">selling_price_per_unit ({{transport_load_Form.billing_units_outgoing_id.name}}) outgoing:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_finance_Form.selling_price_per_unit" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                            <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                        </div>
                                    </div>
                                    <div class="col-span-4">

                                        <label class="block text-sm font-medium leading-6 text-gray-900">transport_rate_basis_id</label>
                                        <select v-model="transport_finance_Form.transport_rate_basis_id"
                                                class="mt-2 block w-2/3 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option v-for="n in props.all_transport_rates" :key="n.id" :value="n.id">
                                                {{ n.name }}
                                            </option>
                                        </select>

                                        <InputError class="mt-2"
                                                    :message="transport_finance_Form.errors.transport_rate_basis_id"/>
                                    </div>
                                    <div  class="col-span-4 ">
                                        <div class="">
                                            <div class="w-1/2">
                                                <label class="block text-sm font-medium leading-6 text-gray-900">transport_rate:</label>
                                                <div class="mt-2">
                                                    <input v-model="transport_finance_Form.transport_rate" type="number"
                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                    <InputError class="mt-2" :message="transport_finance_Form.errors.transport_rate"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="can_adjust_gp" class="col-span-4">
                                        <div class="text-sm mb-2 text-indigo-400">Only Approved users</div>

                                          <div class="flex m-2 p-2 border shadow-xl rounded">
                                              <div class="w-1/2">
                                                  <label class="block text-sm font-medium leading-6 text-gray-900">adjusted_gp:</label>
                                                  <div class="mt-2">
                                                      <input v-model="transport_finance_Form.adjusted_gp" type="number"
                                                             class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                      <InputError class="mt-2" :message="transport_finance_Form.errors.adjusted_gp"/>
                                                  </div>
                                              </div>
                                              <div class="ml-3 w-1/2">
                                                  <label class="block text-sm font-medium leading-6 text-gray-900">adjusted_gp_notes:</label>
                                                  <div class="mt-2">
                                                      <input v-model="transport_finance_Form.adjusted_gp_notes"
                                                             placeholder="Description..." type="text"
                                                             class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                      <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                                  </div>

                                              </div>
                                          </div>
                                    </div>
                                    <div class="col-span-4 flex">

                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_1:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_1" type="number"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>
                                        </div>

                                        <div class="ml-3 w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_desc_1:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_desc_1"
                                                       placeholder="Description..." type="text"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-span-4 flex">

                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_2:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_2" type="number"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>
                                        </div>

                                        <div class="ml-3 w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_desc_2:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_desc_2"
                                                       placeholder="Description..." type="text"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-span-4 flex">

                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_3:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_3" type="number"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>
                                        </div>

                                        <div class="ml-3 w-1/2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900">additional_cost_desc_3:</label>
                                            <div class="mt-2">
                                                <input v-model="transport_finance_Form.additional_cost_desc_3"
                                                       placeholder="Description..." type="text"
                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                <!--                                            <InputError class="mt-2" :message="customerForm.errors.id_reg_no"/>-->
                                            </div>

                                        </div>

                                    </div>
                                    <div class="col-span-6">
                                        <div class="mt-8">

                                            <div class="p-2 m-2 rounded shadow">

                                                <div>
                                                    <div class="px-4 sm:px-0">
                                                        <h3 class="text-base font-semibold leading-7 text-gray-900">
                                                            Calculated
                                                            Fields</h3>
                                                        <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">
                                                            Calculated from
                                                            the upstream capturing:</p>
                                                    </div>
                                                    <div class="mt-6 border-t border-gray-100">
                                                        <dl class="divide-y divide-gray-100">
                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    selling_price
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber( props.transaction.transport_finance.selling_price )}}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    cost_price
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.cost_price) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    weight_ton_incoming
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ props.transaction.transport_finance.weight_ton_incoming }} Tons</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    weight_ton_outgoing
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ props.transaction.transport_finance.weight_ton_outgoing }} Tons</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    cost_price_per_ton
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.cost_price_per_ton) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    selling_price_per_ton
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.selling_price_per_ton) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    load_insurance_per_ton
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.load_insurance_per_ton) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    transport_cost
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.transport_cost) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    total_cost_price
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.total_cost_price) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    gross_profit
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.gross_profit) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    gross_profit_percent
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ props.transaction.transport_finance.gross_profit_percent }} %</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    gross_profit_per_ton
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.gross_profit_per_ton) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    total_supplier_comm
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.total_supplier_comm) }}</span>
                                                                </dd>
                                                            </div>

                                                            <div
                                                                class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                                <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                    total_customer_comm
                                                                </dt>
                                                                <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                    <span
                                                                        class="flex-grow">{{ NiceNumber(props.transaction.transport_finance.total_customer_comm) }}</span>
                                                                </dd>
                                                            </div>


                                                        </dl>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-span-4">

                                        <SecondaryButton @click="updateTransportFinance" class="m-1">
                                            Update
                                        </SecondaryButton>
                                    </div>

                                </div>

                            </form>


                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Assigned Commission</div>


                            <form class="mt-5">

                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <div class="col-span-4 mt-2">

                                        <div class="text-lg ml-6 text-indigo-400">User commissions:</div>

                                        <SecondaryButton @click="viewAssignedNewComm" class="m-6">
                                            Add Comm User (+)
                                        </SecondaryButton>

                                        <div v-if="viewAssignedCommNewModal">
                                            <assigned-comm-modal
                                                :show="viewAssignedCommNewModal" @close="closeAssignedNewComm"
                                                :transport_trans_id="transaction.id"
                                                :transport_finance_id="transaction.transport_finance.id"
                                                :assigned_user_comm="null"
                                                :all_staff="props.all_staff"

                                            />
                                        </div>

                                    </div>

                                    <div class="col-span-4">
                                        <div
                                            v-for="(user_comm,index) in transaction.assigned_user_comm"
                                            :key="user_comm.id">
                                            <div class="ml-5 p-4 border-solid rounded shadow-xl">
                                                <div class="px-4 sm:px-0">
                                                    <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                                                        User Comm {{ index + 1 }}</h3>
                                                    <h3 class="text-base font-semibold leading-7 text-sm text-gray-400">
                                                        Reference {{ user_comm.id }}</h3>
                                                </div>
                                                <div class="mt-6 border-t border-gray-100">
                                                    <dl class="divide-y divide-gray-100">
                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                assigned_user_supplier_id
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{user_comm.assigned_user_supplier.first_name}} {{user_comm.assigned_user_supplier.last_legal_name}}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                assigned_user_customer_id
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{user_comm.assigned_user_customer.first_name}} {{user_comm.assigned_user_customer.last_legal_name}}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                supplier_comm
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ NiceNumber( user_comm.supplier_comm)}}
                                                            </dd>
                                                        </div>

                                                        <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                                            <dt class="text-sm font-medium leading-6 text-gray-900">
                                                                customer_comm
                                                            </dt>
                                                            <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                                                                {{ NiceNumber( user_comm.customer_comm)}}
                                                            </dd>
                                                        </div>
                                                    </dl>
                                                </div>

                                                <div class="mt-6 col-span-4">

                                                    <div v-if="viewAssignedCommModal">

                                                        <assigned-comm-modal
                                                            :show="viewAssignedCommModal" @close="closeAssignedComm"
                                                            :transport_trans_id="transaction.id"
                                                            :transport_finance_id="transaction.transport_finance.id"
                                                            :assigned_user_comm="currentAssignedComm"
                                                            :all_staff="props.all_staff"
                                                        />

                                                    </div>


                                                    <SecondaryButton class="m-1"
                                                                     @click="viewAssignedComm(user_comm)">
                                                        Edit
                                                    </SecondaryButton>

                                                    <SecondaryButton class="m-1"
                                                                     @click="deleteAssignedComm(user_comm.id)">
                                                        Delete
                                                    </SecondaryButton>


                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Statuses</div>


                            <form class="mt-5">

                                <div class="mt-2 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

                                    <div class="col-span-4 ">
                                        <div class="text-sm mb-2 text-indigo-400">Add status</div>

                                        <div class="flex">
                                            <div class="w-1/2">
                                                <label class="block text-sm font-medium leading-6 text-gray-900">Entity:</label>
                                                <div class="mt-2">
                                                    <div class="">
                                                        <select v-model="status_Form.status_entity_id"
                                                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in props.all_status_entities" :key="n.id" :value="n.id">
                                                                {{n.entity}}
                                                            </option>
                                                        </select>

                                                    </div>

                                                </div>
                                            </div>
                                            <div class="ml-3 w-1/2">
                                                <label class="block text-sm font-medium leading-6 text-gray-900">Status:</label>
                                                <div class="mt-2">
                                                    <div class="">
                                                        <select v-model="status_Form.status_type_id"
                                                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in props.all_status_types" :key="n.id" :value="n.id">
                                                                {{n.type}}
                                                            </option>
                                                        </select>

                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-span-4 mt-2">

                                        <SecondaryButton @click="createStatus" class="">
                                            Add Status (+)
                                        </SecondaryButton>

                                    </div>


                                    <div class="mt-5 col-span-4">
                                        <label class="block mb-1 text-gray-500 dark:text-gray-600 font-medium">Active statuses:</label>
                                        <div>


                                            <div class="flex">

                                                <ul class="w-full">

                                                    <li v-for="n in transaction.transport_status" :key="n.id" :value="n.id"
                                                        class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                                        <div class="flex mt-1">
                                                            <div class="flex-none w-1/6">

                                                                <icon name="tick-circle" class="mr-2 w-20 h-20 fill-green-200"/>

                                                            </div>
                                                            <div class="flex-auto font-bold w-3/6">
                                                                {{ n.status_entity.entity }} - {{ n.status_type.type }} on: {{ n.created_at }}
                                                            </div>

                                                            <div class="flex-auto w-1/6">
                                                                <SecondaryButton>
                                                                    DELETE (-)
                                                                </SecondaryButton>
                                                            </div>
                                                        </div>

                                                    </li>

                                                </ul>
                                            </div>

                                        </div>
                                    </div>


                                </div>

                            </form>

                        </div>

                    </div>

                </div>

                <SectionBorder/>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="m-2 p-2">

                        <div class="">
                            <div class="text-lg mb-2 text-indigo-400">Transport Invoice</div>


<!--                            'old_id','transport_trans_id','type','comment','is_active','is_printed'-->

<!--
                            'invoice_id','transport_trans_id','is_invoiced','is_invoice_paid','invoice_no', 'invoice_paid_date','invoice_pay_by_date','invoice_date','invoice_amount','invoice_amount_paid','cost_price','selling_price','status_id','notes'
-->

                            <form class="mt-5">

                                <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">


                                    <div class="flex col-span-4 mt-2">
                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_invoice_Form.is_active"
                                                    :class="[transport_invoice_Form.is_active ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Active</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_invoice_Form.is_printed"
                                                    :class="[transport_invoice_Form.is_printed ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_printed? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Printed</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_invoice_Form.is_invoiced"
                                                    :class="[transport_invoice_Form.is_invoiced ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_invoiced ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Invoiced</span>
                                            </SwitchLabel>
                                        </SwitchGroup>

                                        <SwitchGroup as="div" class="flex m-2 items-center">
                                            <Switch v-model="transport_invoice_Form.is_invoice_paid"
                                                    :class="[transport_invoice_Form.is_invoice_paid ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_invoice_paid ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                            </Switch>
                                            <SwitchLabel as="span" class="ml-3 text-sm">
                                                <span class="font-medium text-gray-900">Paid</span>
                                            </SwitchLabel>
                                        </SwitchGroup>



                                    </div>

                                    <div class="flex col-span-6 mt-1">

                                        <div>
                                            <div>
                                                <VueDatePicker style="width: 250px;"
                                                               v-model="transport_invoice_Form.invoice_date"
                                                               :format="formatInvoiceDate"
                                                               :teleport="true"></VueDatePicker>
                                            </div>

                                            <div class="ml-3 text-sm font-bold">
                                                invoice_date
                                            </div>

                                            <InputError class="mt-2"
                                                        :message="transport_invoice_Form.errors.invoice_date"/>
                                        </div>

                                        <div class="ml-2">
                                            <div>
                                                <VueDatePicker style="width: 250px;"
                                                               v-model="transport_invoice_Form.invoice_pay_by_date"
                                                               :format="formatInvoicePayByDay"
                                                               :teleport="true"></VueDatePicker>
                                            </div>

                                            <div class="ml-3 text-sm font-bold">
                                                invoice_pay_by_date
                                            </div>

                                            <InputError class="mt-2"
                                                        :message="transport_invoice_Form.errors.invoice_pay_by_date"/>
                                        </div>

                                        <div class="ml-2">
                                            <div>
                                                <VueDatePicker style="width: 250px;"
                                                               v-model="transport_invoice_Form.invoice_paid_date"
                                                               :format="formatInvoicePdDay"
                                                               :teleport="true"></VueDatePicker>
                                            </div>

                                            <div class="ml-3 text-sm font-bold">
                                                invoice_paid_date
                                            </div>

                                            <InputError class="mt-2"
                                                        :message="transport_invoice_Form.errors.invoice_paid_date"/>
                                        </div>



                                    </div>

                                    <div class="col-span-4 ">

                                        <div class="flex">
                                            <div class="w-1/2">
                                                <label class="block text-sm font-medium leading-6 text-gray-900">Invoice Status:</label>
                                                <div class="mt-2">
                                                    <div class="">
                                                        <select v-model="transport_invoice_Form.status_id"
                                                                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                            <option v-for="n in props.all_invoice_statuses" :key="n.id" :value="n.id">
                                                                {{n.name}}
                                                            </option>
                                                        </select>

                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">invoice_no:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_invoice_Form.invoice_no" type="text"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_invoice_Form.errors.invoice_no"/>

                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">invoice_amount:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_invoice_Form.invoice_amount" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_invoice_Form.errors.invoice_amount"/>

                                    </div>

                                    <div class="col-span-4">
                                        <label class="block text-sm font-medium leading-6 text-gray-900">invoice_amount_paid:</label>
                                        <div class="mt-2">
                                            <input v-model="transport_invoice_Form.invoice_amount_paid" type="number"
                                                   class="block w-full lg:w-2/3 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                        </div>

                                        <InputError class="mt-2"
                                                    :message="transport_invoice_Form.errors.invoice_amount_paid"/>

                                    </div>

                                    <div class="col-span-4">
                                        <label
                                            class="block text-sm font-medium leading-6 text-gray-900">Notes:</label>
                                        <AreaInput
                                            v-model="transport_invoice_Form.notes"
                                            :rows=4
                                            placeholder="Optional comments..."
                                            type="text"
                                            class="mt-1 block w-1/3"

                                        />

                                        <InputError class="mt-2"
                                                    :message="transport_job_Form.errors.loading_instructions"/>
                                    </div>

                                    <div class="col-span-4">

                                        <SecondaryButton @click="updateTransportInvoice" class="m-1">
                                            Update
                                        </SecondaryButton>
                                    </div>

                                </div>


                            </form>

                        </div>

                    </div>

                </div>


            </div>
        </div>
    </AppLayout>
</template>
