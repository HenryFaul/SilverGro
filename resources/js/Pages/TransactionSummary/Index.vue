<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed, inject, onBeforeMount, ref, watch} from 'vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import {router, useForm, usePage,Link} from "@inertiajs/vue3";
import {debounce, throttle} from 'lodash'
import PaginationModified from  "@/Components/UI/PaginationModified.vue";
import Icon from "@/Components/Icon.vue";
import TradeSlideOver from  "@/Components/UI/TradeSlideOver.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import {EllipsisHorizontalIcon,CheckIcon, ChevronUpDownIcon, PaperClipIcon, XCircleIcon, InformationCircleIcon, ExclamationTriangleIcon, XMarkIcon, TruckIcon} from '@heroicons/vue/20/solid';
import InputError from '@/Components/InputError.vue';
import AreaInput from '@/Components/AreaInput.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import DriverVehicleModal from "@/Components/UI/DriverVehicleModal.vue";
import DriverVehicleModalAdd from "@/Components/UI/DriverVehicleModal.vue";
import BaseTooltip from "@/Components/UI/BaseTooltip.vue";

const swal = inject('$swal');

import {
    Switch,
    SwitchGroup,
    SwitchLabel,
    Listbox,
    ListboxButton,
    ListboxLabel,
    ListboxOption,
    ListboxOptions,
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue';

import ContractLinkModal from "@/Components/UI/ContractLinkModal.vue";

let dayIncluded = (_date) => {
    let _day = NiceDay(_date);
    switch(_day) {
        case 1:
            return mon.value;
        case 2:
            return tue.value;
        case 3:
            return wed.value;
        case 4:
            return thu.value;
        case 5:
            return fri.value;
        case 6:
            return sat.value;
        case 7:
            return sun.value;
        default:
            return false;
    }
};

let NiceDay = (_date) => {
    return new Date(_date).getDay()
};

let NiceTDate = (date) => {
    const _date = new Date(date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const dayString = (_date.toLocaleString('en', {weekday: 'long', timeZone: 'Africa/Johannesburg'})).toUpperCase();
    const year = _date.getFullYear();
    return `${dayString} ${day}/${month}/${year}`;
};

let TrunkCateText = (_text) => {
    return _text.length > 40 ? _text.slice(0, 40) + "..." : _text;
}

const format = () => {
    const _date = new Date(filterForm.end_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

const formatStart = () => {
    const _date = new Date(filterForm.start_date);
    const day = _date.getDate();
    const month = (_date.toLocaleString('en', {month: 'long', timeZone: "Africa/Johannesburg"})).toUpperCase();
    const year = _date.getFullYear();
    return `${day}/${month}/${year}`;
}

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

const props = defineProps({
    transactions:Object,
    selected_transaction:Object,
    filters: Object,
    start_date: String,
    end_date: String,
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
    rules_with_approvals:Object,
    linked_trans: Object,
    deal_ticket:Object,
    transport_order:Object,
    purchase_order:Object,
    sales_order:Object
});

onBeforeMount(async () => {

});

const tabs = [
    { id:0, name: 'Supplier', current: true },
    { id:1, name: 'Product',  current: false },
    { id:2, name: 'Customer', current: false },
    { id:3,name: 'Transport',  current: false },
    { id:4, name: 'Pricing',  current: false },
    { id:5, name: 'Invoice',  current: false },
    { id:6, name: 'Process Control', current: false },
    { id:7, name: 'Linked Contracts',  current: false },
    { id:8, name: 'Contracts',  current: false },
    { id:9,name: 'Log', current: false },
    { id:10,name: 'Admin', current: false },
];

let supplierQuery = ref('');

const filteredSuppliers = computed(() =>
    supplierQuery.value === ''
        ? props.all_suppliers
        : props.all_suppliers.filter((supplier) => {
            return supplier.last_legal_name.toLowerCase().includes(supplierQuery.value.toLowerCase())
        })
);


const permissions = computed(() => usePage().props.permissions)
const roles_permissions = computed(() => usePage().props.roles_permissions)
const can_adjust_gp = computed(() => usePage().props.roles_permissions.permissions.includes("edit_adjusted_gp"))


let isLoading = ref(false);
let isUpdating = ref(false);

const selectedTabId = ref(0);
const selectTab = (id) => {
    selectedTabId.value = id;
};
const viewTradeSlideOver = ref(false);

const showTradeSlideOver = () => {
    viewTradeSlideOver.value = true;
};
const closeTradeSlideOver = () => {
    viewTradeSlideOver.value = false;
};
const closeContractLink = () => {
    viewContractLinkModal.value = false;
};


const filterForm = useForm({
    isActive: props.filters.isActive ?? null,
    field: props.filters.field ?? null,
    direction: props.filters.direction ?? "asc",
    show: props.filters.show ?? 5,
    supplier_name:props.filters.supplier_name ?? null,
    customer_name:props.filters.customer_name ?? null,
    transporter_name:props.filters.transporter_name ?? null,
    product_name:props.filters.product_name ?? null,
    start_date:props.filters.start_date ?? null,
    end_date:props.filters.end_date ?? null,
    contract_type_id:props.filters.contract_type_id ?? null,
    id:props.filters.id ?? null,
    selected_trans_id:props.selected_transaction.id ?? null,
})


let filter =  debounce( () => {

    isLoading.value = true;
    filterForm.get(
        route('transaction_summary.index'),
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: visit => {
                 updateSelectValues();
                isLoading.value = false;
            },
        },
    );

},150);

let sort = (field) => {
    filterForm.field = field;
    filterForm.direction = filterForm.direction === 'asc' ? 'desc' : 'asc';
    filter();
}

watch(
    () => filterForm.supplier_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.customer_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.transporter_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.product_name,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.show,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.start_date,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.end_date,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.contract_type_id,
    (exampleField, prevExampleField) => {
        filter();
    }
);

watch(
    () => filterForm.id,
    (exampleField, prevExampleField) => {
        filter();
    }
);


let mon = ref(true);
let tue = ref(true);
let wed = ref(true);
let thu = ref(true);
let fri = ref(true);
let sat = ref(true);
let sun = ref(true);

let filteredTrans = computed(() =>
    (mon.value && tue.value && wed.value && thu.value && fri.value && sat.value && sun.value )
        ? props.transactions.data
        : props.transactions.data.filter((trans) => {
            return dayIncluded(trans.transport_date_earliest)
        })
);

let updateSelectedTrans = async (_id) => {

    filterForm.selected_trans_id = _id;
    filter();


};

const clear = () => {
    filterForm.supplier_name = null;
    filterForm.customer_name = null;
    filterForm.transporter_name = null;
    filterForm.product_name = null;
    filterForm.start_date = null;
    filterForm.end_date = null;
    filterForm.contract_type_id = null;
    filterForm.id = null;

    mon.value=true;
    tue.value=true;
    wed.value=true;
    thu.value=true;
    fri.value=true;
    sat.value=true;
    sun.value=true;

    filter();
}

let updateSelectValues = async () => {

    //transport_approval_Form
    transport_approval_Form.transport_trans_id= props.selected_transaction.id;
    transport_approval_Form.transport_job_id= props.selected_transaction.transport_job.id;
    transport_approval_Form.deal_ticket_id= props.selected_transaction.deal_ticket.id;

    //temp_form
    temp_form.transport_trans_id= props.selected_transaction.id;

    //transport_trans_Form
     transport_trans_Form.contract_type_id = props.contract_types.find(element => element.id === props.selected_transaction.contract_type_id);
     transport_trans_Form.product_id = props.all_products.find(element => element.id === props.selected_transaction.product_id);
     transport_trans_Form.supplier_id = props.all_suppliers.find(element => element.id === props.selected_transaction.supplier_id);
     transport_trans_Form.customer_id = props.all_customers.find(element => element.id === props.selected_transaction.customer_id);
     transport_trans_Form.transporter_id = props.all_transporters.find(element => element.id === props.selected_transaction.transporter_id);

     //transport_load_Form
    transport_load_Form.confirmed_by_id= props.all_staff.find(element => element.id === props.selected_transaction.transport_load.confirmed_by_id);
    transport_load_Form.confirmed_by_type_id = props.confirmation_types.find(element => element.id === props.selected_transaction.transport_load.confirmed_by_type_id);
    transport_load_Form.packaging_incoming_id = props.all_packaging.find(element => element.id === props.selected_transaction.transport_load.packaging_incoming_id);
    transport_load_Form.packaging_outgoing_id = props.all_packaging.find(element => element.id === props.selected_transaction.transport_load.packaging_outgoing_id);
    transport_load_Form. product_source_id = props.all_product_sources.find(element => element.id === props.selected_transaction.transport_load.product_source_id);
    transport_load_Form.billing_units_incoming_id = props.all_billing_units.find(element => element.id === props.selected_transaction.transport_load.billing_units_incoming_id);
    transport_load_Form.billing_units_outgoing_id = props.all_billing_units.find(element => element.id === props.selected_transaction.transport_load.billing_units_outgoing_id);
    transport_load_Form.collection_address_id = transport_trans_Form.supplier_id.addressable.find(element => element.id === props.selected_transaction.transport_load.collection_address_id);
    transport_load_Form.delivery_address_id = transport_trans_Form.customer_id.addressable.find(element => element.id === props.selected_transaction.transport_load.delivery_address_id);

    //Sales Order

    salesOrder_Form.transport_trans_id = props.sales_order.transport_trans_id;
    salesOrder_Form.confirmed_by_type_id = props.sales_order.confirmed_by_type_id;
    salesOrder_Form.is_active = props.sales_order.is_active;
    salesOrder_Form.is_so_conf_sent = props.sales_order.is_so_conf_sent;
    salesOrder_Form.is_so_conf_received = props.sales_order.is_so_conf_received;

    //Purchase Order
    purchaseOrder_Form.transport_trans_id = props.purchase_order.transport_trans_id;
    purchaseOrder_Form.confirmed_by_type_id= props.purchase_order.confirmed_by_type_id;
    purchaseOrder_Form.is_active= props.purchase_order.is_active;
    purchaseOrder_Form.is_po_sent= props.purchase_order.is_po_sent;
    purchaseOrder_Form.is_po_received= props.purchase_order.is_po_received;
}


let transport_trans_Form = useForm({

    product_id: props.all_products.find(element => element.id === props.selected_transaction.product_id),
    supplier_id: props.all_suppliers.find(element => element.id === props.selected_transaction.supplier_id),
    customer_id: props.all_customers.find(element => element.id === props.selected_transaction.customer_id),
    transporter_id: props.all_transporters.find(element => element.id === props.selected_transaction.transporter_id),
    contract_type_id: props.contract_types.find(element => element.id === props.selected_transaction.contract_type_id),
    contract_no: props.selected_transaction.contract_no,
    old_id: props.selected_transaction.old_id,
    include_in_calculations: props.selected_transaction.include_in_calculations,
    transport_date_earliest: props.selected_transaction.transport_date_earliest,
    transport_date_latest: props.selected_transaction.transport_date_latest,
    suppliers_notes: props.selected_transaction.suppliers_notes,
    delivery_notes: props.selected_transaction.delivery_notes,
    product_notes: props.selected_transaction.product_notes,
    customer_notes: props.selected_transaction.customer_notes,
    traders_notes: props.selected_transaction.traders_notes,
    transport_notes: props.selected_transaction.transport_notes,
    pricing_notes: props.selected_transaction.pricing_notes,
    process_notes: props.selected_transaction.process_notes,
    document_notes: props.selected_transaction.document_notes,
    transaction_notes: props.selected_transaction.transaction_notes,
    traders_notes_supplier: props.selected_transaction.traders_notes_supplier,
    traders_notes_customer: props.selected_transaction.traders_notes_customer,
    traders_notes_transport: props.selected_transaction.traders_notes_transport,
    is_transaction_done: props.selected_transaction.is_transaction_done,
});

let transport_load_Form = useForm({
    confirmed_by_id: props.all_staff.find(element => element.id === props.selected_transaction.transport_load.confirmed_by_id),
    confirmed_by_type_id: props.confirmation_types.find(element => element.id === props.selected_transaction.transport_load.confirmed_by_type_id),
    packaging_incoming_id: props.all_packaging.find(element => element.id === props.selected_transaction.transport_load.packaging_incoming_id),
    packaging_outgoing_id: props.all_packaging.find(element => element.id === props.selected_transaction.transport_load.packaging_outgoing_id),
    product_source_id: props.all_product_sources.find(element => element.id === props.selected_transaction.transport_load.product_source_id),
    billing_units_incoming_id: props.all_billing_units.find(element => element.id === props.selected_transaction.transport_load.billing_units_incoming_id),
    billing_units_outgoing_id: props.all_billing_units.find(element => element.id === props.selected_transaction.transport_load.billing_units_outgoing_id),
    collection_address_id: transport_trans_Form.supplier_id.addressable.find(element => element.id === props.selected_transaction.transport_load.collection_address_id),
    delivery_address_id: transport_trans_Form.customer_id.addressable.find(element => element.id === props.selected_transaction.transport_load.delivery_address_id),
    product_grade_perc: props.selected_transaction.transport_load.product_grade_perc,
    no_units_incoming: props.selected_transaction.transport_load.no_units_incoming,
    no_units_outgoing: props.selected_transaction.transport_load.no_units_outgoing,
    is_weighbridge_certificate_received: props.selected_transaction.transport_load.is_weighbridge_certificate_received,
    delivery_note: props.selected_transaction.transport_load.delivery_note,
    calculated_route_distance: props.selected_transaction.transport_load.calculated_route_distance,

});

let transport_job_Form = useForm({
    customer_order_number: props.selected_transaction.transport_job.customer_order_number,
    supplier_loading_number: props.selected_transaction.transport_job.supplier_loading_number,
    is_multi_loads: props.selected_transaction.transport_job.is_multi_loads,
    is_approved: props.selected_transaction.transport_job.is_approved,
    is_transport_costs_inc_price: props.selected_transaction.transport_job.is_transport_costs_inc_price,
    is_product_zero_rated: props.selected_transaction.transport_job.is_product_zero_rated,
    offloading_hours_from_id: props.selected_transaction.transport_job.offloading_hours_from_id,
    offloading_hours_to_id: props.selected_transaction.transport_job.offloading_hours_to_id,
    loading_hours_from_id: props.selected_transaction.transport_job.loading_hours_from_id,
    loading_hours_to_id: props.selected_transaction.transport_job.loading_hours_to_id,
    load_insurance_per_ton: props.selected_transaction.transport_job.load_insurance_per_ton,
    total_load_insurance: props.selected_transaction.transport_job.total_load_insurance,
    number_loads: props.selected_transaction.transport_job.number_loads,
    loading_instructions: props.selected_transaction.transport_job.loading_instructions,
    offloading_instructions: props.selected_transaction.transport_job.offloading_instructions,
});

let transport_finance_Form = useForm({
    transport_rate_basis_id: props.selected_transaction.transport_finance.transport_rate_basis_id,
    cost_price_per_unit: props.selected_transaction.transport_finance.cost_price_per_unit,
    selling_price_per_unit: props.selected_transaction.transport_finance.selling_price_per_unit,
    transport_rate:props.selected_transaction.transport_finance.transport_rate,
    additional_cost_1: props.selected_transaction.transport_finance.additional_cost_1,
    additional_cost_2: props.selected_transaction.transport_finance.additional_cost_2,
    additional_cost_3: props.selected_transaction.transport_finance.additional_cost_3,
    additional_cost_desc_1: props.selected_transaction.transport_finance.additional_cost_desc_1,
    additional_cost_desc_2: props.selected_transaction.transport_finance.additional_cost_desc_2,
    additional_cost_desc_3: props.selected_transaction.transport_finance.additional_cost_desc_3,
    adjusted_gp: props.selected_transaction.transport_finance.adjusted_gp,
    adjusted_gp_notes: props.selected_transaction.transport_finance.adjusted_gp_notes,

});

let transport_invoice_Form = useForm({
    transport_trans_id: props.selected_transaction.id,
    old_id:props.selected_transaction.transport_invoice.old_id,
    is_active:props.selected_transaction.transport_invoice.is_active,
    is_printed:props.selected_transaction.transport_invoice.is_printed,
    invoice_id:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_id,
    is_invoiced:props.selected_transaction.transport_invoice.transport_invoice_details.is_invoiced,
    is_invoice_paid:props.selected_transaction.transport_invoice.transport_invoice_details.is_invoice_paid,
    invoice_no:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_no,
    invoice_paid_date:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_paid_date,
    invoice_pay_by_date:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_pay_by_date,
    invoice_date:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_date,
    invoice_amount:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_amount,
    invoice_amount_paid:props.selected_transaction.transport_invoice.transport_invoice_details.invoice_amount_paid,
    status_id:props.selected_transaction.transport_invoice.transport_invoice_details.status_id,
    notes:props.selected_transaction.transport_invoice.transport_invoice_details.notes
});

let status_Form = useForm({
    transport_trans_id: props.selected_transaction.id,
    status_entity_id:1,
    status_type_id:1
});

let transport_approval_Form = useForm({
    transport_trans_id: props.selected_transaction.id,
    transport_job_id: props.selected_transaction.transport_job.id,
    deal_ticket_id: props.selected_transaction.deal_ticket.id,
});

//'old_id','transport_trans_id','confirmed_by_type_id','confirmed_by_id','type','comment','is_active','is_printed','is_so_conf_sent','is_so_conf_received'
let salesOrder_Form = useForm({
    transport_trans_id: props.sales_order.transport_trans_id,
    confirmed_by_type_id: props.sales_order.confirmed_by_type_id,
    is_active: props.sales_order.is_active,
    is_so_conf_sent: props.sales_order.is_so_conf_sent,
    is_so_conf_received: props.sales_order.is_so_conf_received
});

let purchaseOrder_Form = useForm({
    transport_trans_id: props.purchase_order.transport_trans_id,
    confirmed_by_type_id: props.purchase_order.confirmed_by_type_id,
    is_active: props.purchase_order.is_active,
    is_po_sent: props.purchase_order.is_po_sent,
    is_po_received: props.purchase_order.is_po_received
});


let transportOrder_Form = useForm({
    transport_trans_id: props.transport_order.transport_trans_id,
    confirmed_by_type_id: props.transport_order.confirmed_by_type_id,
    is_active: props.transport_order.is_active,
    is_to_sent: props.transport_order.is_to_sent,
    is_to_received: props.transport_order.is_to_received
});


//Errors

let ErrorsTrans = computed(() => Object.keys(transport_trans_Form.errors).length === 0 && transport_trans_Form.errors.constructor === Object);
let ErrorsLoad = computed(() => Object.keys(transport_load_Form.errors).length === 0 && transport_load_Form.errors.constructor === Object);
let ErrorsJob = computed(() => Object.keys(transport_job_Form.errors).length === 0 && transport_job_Form.errors.constructor === Object);
let ErrorsFinance = computed(() => Object.keys(transport_finance_Form.errors).length === 0 && transport_finance_Form.errors.constructor === Object);
let ErrorsInvoice = computed(() => Object.keys(transport_invoice_Form.errors).length === 0 && transport_invoice_Form.errors.constructor === Object);
let ErrorsApproval = computed(() => Object.keys(transport_approval_Form.errors).length === 0 && transport_approval_Form.errors.constructor === Object);


let isErrors = computed(() =>ErrorsTrans || ErrorsJob || ErrorsJob || ErrorsFinance || ErrorsInvoice || ErrorsApproval );


//Form CRUD

const updateAll = () => {
    updateTransportTrans();
}

const updateTransportTrans = () => {

    isUpdating.value = true;

    transport_trans_Form.put(route('transport_transaction.update', props.selected_transaction.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                updateTransportLoad();
            },
            onError: (error) => {
                isUpdating.value = false;
                alert('Something went wrong on the Transaction')
                console.log(error)
            }
        }
    );
}

const updateTransportLoad = () => {
    transport_load_Form.put(route('transport_load.update', props.selected_transaction.transport_load.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                updateTransportJob();
            },
            onError: (error) => {
                isUpdating.value = false;
                alert('Something went wrong on the Load')
                console.log(error)
            }
        }
    );
}

const updateTransportJob = () => {
    transport_job_Form.put(route('transport_job.update', props.selected_transaction.transport_job.id),
        {
            preserveScroll: true,
            onSuccess: () => {
               updateTransportFinance();
            },
            onError: (error) => {
                isUpdating.value = false;
                alert('Something went wrong on the transport Job')
                console.log(error)
            }
        }
    );
}

const updateTransportFinance = () => {
    transport_finance_Form.put(route('transport_finance.update', props.selected_transaction.transport_finance.id),
        {
            preserveScroll: true,
            onSuccess: () => {

               updateTransportInvoice();
            },
            onError: (error) => {
                isUpdating.value = false;
                alert('Something went wrong on finance')
                console.log(error)
            }
        }
    );
}

const updateTransportInvoice = () => {
    transport_invoice_Form.put(route('transport_invoice.update', props.selected_transaction.transport_invoice.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal('Updated');
                isUpdating.value = false;
            },
            onError: (error) => {
                isUpdating.value = false;
                alert('Something went wrong')
                console.log(error)
            }
        }
    );
}

const activateSalesOrder = () => {

    isUpdating.value = true;

    salesOrder_Form.post(route('sales_order.activate'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const activatePurchaseOrder = () => {

    isUpdating.value = true;

    purchaseOrder_Form.post(route('purchase_order.activate'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const activateTransportOrder = () => {

    isUpdating.value = true;

    transportOrder_Form.post(route('transport_order.activate'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const sendSalesOrder = () => {

    isUpdating.value = true;

    salesOrder_Form.post(route('sales_order.send'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const sendPurchaseOrder = () => {

    isUpdating.value = true;

    purchaseOrder_Form.post(route('purchase_order.send'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const sendTransportOrder = () => {

    isUpdating.value = true;

    transportOrder_Form.post(route('transport_order.send'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const receiveSalesOrder = () => {

    isUpdating.value = true;

    salesOrder_Form.post(route('sales_order.received'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const receivePurchaseOrder = () => {

    isUpdating.value = true;

    purchaseOrder_Form.post(route('purchase_order.received'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const receiveTransportOrder = () => {

    isUpdating.value = true;

    transportOrder_Form.post(route('transport_order.received'),
        {
            preserveScroll: true,
            onSuccess: () => {
                swal(usePage().props.jetstream.flash?.banner || '');
            },
            onError: (error) => {
                isUpdating.value = false;
                swal(usePage().props.jetstream.flash?.banner || '');
            }
        }
    );
}

const temp_form = useForm({
    transport_trans_id: props.selected_transaction.id,
});


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

const createApproval = () => {
    transport_approval_Form.post(route('trans_approval.approve'), {
        preserveScroll: true,
        onSuccess: () => {
            filter();
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
            filter();
            swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

const createFinalDealTicket = () => {
    temp_form.post(route('pdf_report.deal_ticket_final'), {
        preserveScroll: true,
        onSuccess: () => {
            swal(usePage().props.jetstream.flash?.banner || '');
        },
        onError: (e) => {
            console.log(e);
        },
    });
};

//pdf_report.deal_ticket_final
const downloadDealTicket = () => {

    axios.get(route('pdf_report.deal_ticket_final.download',props.deal_ticket.report_path)).then((res) => {

    });
};

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

let billingUnitsIncomingQuery = ref('');

const filteredBillingUnitsIncoming = computed(() =>
    billingUnitsIncomingQuery.value === ''
        ? props.all_billing_units
        : props.all_billing_units.filter((type) => {
            return type.name.toLowerCase().includes(billingUnitsIncomingQuery.value.toLowerCase())
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

let productQuery = ref('');

const filteredProducts = computed(() =>
    productQuery.value === ''
        ? props.all_products
        : props.all_products.filter((product) => {
            return product.name.toLowerCase().includes(productQuery.value.toLowerCase())
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

let billingUnitsOutgoingQuery = ref('');

const filteredBillingUnitsOutgoing = computed(() =>
    billingUnitsOutgoingQuery.value === ''
        ? props.all_billing_units
        : props.all_billing_units.filter((type) => {
            return type.name.toLowerCase().includes(billingUnitsOutgoingQuery.value.toLowerCase())
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

let contractTypeQuery = ref('');

const filteredContractTypes = computed(() =>
    contractTypeQuery.value === ''
        ? props.contract_types
        : props.contract_types.filter((type) => {
            return type.name.toLowerCase().includes(contractTypeQuery.value.toLowerCase())
        })
);

let currentDriverVehicle = ref(null);
let viewDriverVehicleModal = ref(false);
let viewDriverVehicleNewModal = ref(false);


const viewDriverVehicle = (driver_vehicle) => {
    currentDriverVehicle.value = driver_vehicle;
    viewDriverVehicleModal.value = true;
};

const viewDriverNewVehicle = () => {
    viewDriverVehicleNewModal.value = true;
};

const closeDriverVehicleModal = () => {
    viewDriverVehicleModal.value = false;
    viewDriverVehicleNewModal.value = false;
};

let viewContractLinkModal = ref(false);

const viewContractLink = () => {
    viewContractLinkModal.value = true;

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

</script>

<template>
    <AppLayout title="Transaction Summary">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transaction Summary
            </h2>
        </template>

        <div class="py-2">

            <div class="bg-white m-2 p-2 shadow-xl sm:rounded-lg">

                    <div >
                        <div class="px-4 sm:px-6 lg:px-8">
                            <div class="mt-3 flow-root">
                                <div class="-mx-4 -my-2 sm:-mx-6 lg:-mx-8" >
                                    <div class="inline-block min-w-full py-2 align-middle">
                                        <div class="ml-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">

                                            <div class="flex col-span-6">
                                                <div>
                                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                                        Start Date
                                                    </div>
                                                    <div class="w-48">
                                                        <VueDatePicker  v-model="filterForm.start_date"
                                                                       :format="formatStart"
                                                                       :teleport="true"
                                                                       ></VueDatePicker>
                                                    </div>

                                                </div>
                                                <div class="ml-2">
                                                    <div class="ml-3 text-indigo-400 text-sm font-bold">
                                                        End Date
                                                    </div>
                                                    <div class="w-48">
                                                        <VueDatePicker v-model="filterForm.end_date"
                                                                       :format="format"
                                                                       :teleport="true"
                                                                      ></VueDatePicker>
                                                    </div>
                                                </div>

                                                <div class="mt-5 ml-2">
                                                    <select v-model="filterForm.contract_type_id"
                                                            class="input-filter-l  w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option  :value="null">All contracts</option>

                                                        <option v-for="n in contract_types" :key="n.id" :value="n.id">
                                                            {{n.name}}
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mt-5 ml-2">
                                                    <select v-model="filterForm.show"
                                                            class="input-filter-l  w-32 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <option :value=5>5</option>
                                                        <option :value=10>10</option>
                                                        <option :value=25>25</option>
                                                        <option :value=100>100</option>
                                                        <option :value=200>200</option>
                                                        <option :value=500>500</option>
                                                    </select>

                                                </div>


                                            </div>
                                            <div class="col-span-4 flex">
                                                <input v-model.number="filterForm.supplier_name" aria-label="Search" class="block w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="supplier name..."
                                                       type="search"/>

                                                <input v-model.number="filterForm.customer_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="customer name..."
                                                       type="search"/>

                                                <input v-model.number="filterForm.transporter_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="transporter name..."
                                                       type="search"/>

                                                <input v-model.number="filterForm.product_name" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="product name..."
                                                       type="search"/>

                                                <input v-model.number="filterForm.id" aria-label="Search" class="block ml-2 w-48 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                                       placeholder="contract no..."
                                                       type="search"/>

                                            </div>
                                            <div class="col-span-4 flex">

                                                <div>
                                                    <secondary-button class="" @click="filter">Search</secondary-button>
                                                    <secondary-button class=" ml-1" @click="clear">Clear</secondary-button>
                                                    <secondary-button class=" ml-1"  @click="showTradeSlideOver">Add (+)</secondary-button>
                                                </div>

                                                <div class="flex ml-6">
                                                    <div class="relative flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="mon" v-model="mon" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="mon" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="mon">Mon</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="tue" v-model="tue" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="tue" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="tue">Tue</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="wed" v-model="wed" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="wed" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="wed">Wed</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="thu" v-model="thu" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="thu" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="thu">Thu</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="fri" v-model="fri" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="fri" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="fri">Fri</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="sat" v-model="sat" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="sat" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="sat">Sat</label>
                                                        </div>
                                                    </div>
                                                    <div class="relative ml-2 flex items-start">
                                                        <div class="flex h-6 items-center">
                                                            <input id="sun" v-model="sun" aria-describedby="candidates-description" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" name="sun" type="checkbox" />
                                                        </div>
                                                        <div class="ml-3 text-sm leading-6">
                                                            <label class="font-medium text-gray-900" for="sun">Sun</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-span-4 mb-3">

                                            </div>

                                        </div>

                                        <div>
                                            <trade-slide-over :show="viewTradeSlideOver" @close="closeTradeSlideOver"  />
                                        </div>
                                        <div class="">
                                            <div class="">
                                                <table class="min-w-full border-separate border-spacing-0">
                                                    <thead>
                                                    <tr>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:pl-6 lg:pl-8" scope="col">ID</th>
                                                        <th class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter sm:table-cell" scope="col">TYPE</th>
                                                        <th class="sticky top-0 z-10 hidden border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter lg:table-cell" scope="col">DATE</th>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter" scope="col">SUPPLIER</th>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter" scope="col">CUSTOMER</th>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter" scope="col">TRANSPORTER</th>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter" scope="col">PRODUCT</th>
                                                        <th class="sticky top-0 z-10 border-b border-gray-300 bg-white bg-opacity-75 px-3 py-3.5 text-left text-sm font-semibold text-gray-900 backdrop-blur backdrop-filter" scope="col">NOTES</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <tr @click="updateSelectedTrans(transaction.id)"  v-for="(transaction, index) in filteredTrans"
                                                        :key="transaction.id"
                                                        :class="[transaction.id === props.selected_transaction.id  ? 'bg-indigo-300' : '', 'hover:bg-gray-100 text-sm focus-within:bg-gray-100']"
                                                    >

                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 lg:pl-8']">
                                                            {{ transaction.id}}
                                                        </td>
                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'whitespace-nowrap hidden px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{transaction.contract_type.name}}
                                                        </td>

                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{ NiceTDate( transaction.transport_date_earliest)}}
                                                        </td>

                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{transaction.supplier.last_legal_name}}
                                                        </td>
                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{transaction.customer.last_legal_name}}
                                                        </td>
                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{transaction.transporter.last_legal_name}}
                                                        </td>

                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">
                                                            {{transaction.product.name}}
                                                        </td>

                                                        <td :class="[transaction !== filteredTrans.length - 1 ? 'border-b border-gray-200' : '', 'px-3 py-4 text-sm text-gray-500 sm:table-cell']">

                                                            <div v-if="transaction.process_notes">

                                                                <base-tooltip :content="transaction.process_notes">
                                                                    {{ TrunkCateText(transaction.process_notes) }}
                                                                </base-tooltip>

                                                            </div>
                                                            <div v-else>None...</div>

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

            </div>

            <div class="bg-white m-2 p-2  shadow-xl sm:rounded-lg">

                <div>
                    <div class="px-4 sm:px-6 lg:px-8">


                        <div>
                            <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                                <div class="md:flex md:items-center md:justify-between">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">Selected Transaction</h3>
                                    <div class="mt-3 flex md:absolute md:right-0 md:top-3 md:mt-0">
                                        <button class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" type="button">
                                            {{selected_transaction.contract_type.name}} {{selected_transaction.id}}
                                        </button>
                                        <button @click.prevent="updateAll"  class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" type="button">
                                            <exclamation-triangle-icon v-if="isUpdating" class="w-3 h-3 animate-spin mr-2 fill-white"/>
                                            Update
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="hidden sm:block">
                                        <nav  class="-mb-px flex space-x-8">
                                            <button v-for="tab in tabs" @click="selectTab(tab.id)" :key="tab.id" :class="[tab.id === selectedTabId ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700', 'whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium']">{{ tab.name }}</button>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            <div class="m-2 p-2">

                                <div class="mb-2" v-if="!isErrors">
                                    <div class="rounded-md bg-red-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <XCircleIcon class="h-5 w-5 text-red-400" aria-hidden="true" />
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">There were errors when updating. Fix & update again.</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <ul role="list" class="list-disc space-y-1 pl-5">
                                                    <li v-if="!ErrorsTrans">Transaction errors</li>
                                                    <li v-if="!ErrorsJob">Job errors</li>
                                                    <li v-if="!ErrorsLoad">Load errors</li>
                                                    <li v-if="!ErrorsFinance">Finance errors</li>
                                                    <li v-if="!ErrorsInvoice">Invoice errors</li>
                                                    <li v-if="!ErrorsApproval">Approval errors</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                </div>

                                <div v-if="isLoading">
                                <span class="text-lg text-indigo-400">Loading...</span>
                                </div>

                                <div v-else>
                                    <div v-if="selectedTabId === 0">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Supplier Details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <Combobox as="div" v-model="transport_trans_Form.supplier_id">

                                                                    <div class="relative mt-2">
                                                                        <ComboboxInput
                                                                            class="w-80 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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

                                                                                <ul>
                                                                                    <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
                                                                                    <span
                                                                                        :class="['block truncate', selected && 'font-semibold']">
                                                                                      {{ supplier.last_legal_name }}
                                                                                    </span>

                                                                                        <span v-if="selected"
                                                                                              :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
                                                                              <CheckIcon class="h-5 w-5"
                                                                                         aria-hidden="true"/></span>
                                                                                    </li>
                                                                                </ul>


                                                                            </ComboboxOption>
                                                                        </ComboboxOptions>
                                                                    </div>
                                                                </Combobox>
                                                            </div>
                                                        </dd>

                                                    </div>

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Supplier Account Details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Payment Terms</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.supplier.terms_of_payment.value}}</div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Collection Address</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <div class="mt-2">

                                                                    <Combobox as="div" v-model="transport_load_Form.collection_address_id">
                                                                        <div class="relative mt-2">
                                                                            <ComboboxInput
                                                                                class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                                    <ul>
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
                                                                                    </ul>
                                                                                </ComboboxOption>
                                                                            </ComboboxOptions>
                                                                        </div>
                                                                    </Combobox>

                                                                    <InputError class="mt-2" :message="transport_load_Form.errors['collection_address_id.id']"/></div>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <div>
                                                            <div v-if="transport_load_Form.collection_address_id">
                                                                <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                    Selected Collection Address:</h3>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 1</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.collection_address_id.line_1 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 2</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.collection_address_id.line_2 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 3</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.collection_address_id.line_3 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Country</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.collection_address_id.country }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Code</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.collection_address_id.code }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                            </div>

                                                            <div v-else>
                                                                No supplier addresses loaded...
                                                            </div>

                                                        </div>
                                                    </div>
                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Product Details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <dt class="text-gray-500">Product</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.product.name}}</div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing Units incoming</dt>
                                                        <dd class="text-gray-700">

                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_incoming_id">

                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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

                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Packaging</dt>
                                                        <dd class="text-gray-700">

                                                            <Combobox as="div" v-model="transport_load_Form.packaging_incoming_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500"> Cost Price / Unit</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.transport_finance.cost_price_per_unit}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Cost Price / Ton</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.transport_finance.cost_price_per_ton}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Total Cost Price</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.total_cost_price)}}</div>
                                                        </dd>
                                                    </div>

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Supplier Notes</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="supplier_notes"
                                                            v-model="transport_trans_Form.suppliers_notes"
                                                            :rows=12
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />

                                                        <InputError class="mt-2"
                                                                    :message="transport_trans_Form.errors.suppliers_notes"/>

                                                    </div>

                                                </dl>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 1">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Incoming Product</div>
                                                    <div class="text-sm font-light text-gray-900">(From Supplier)</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Product</dt>
                                                        <dd class="text-gray-700">
                                                            <div>
                                                                <Combobox as="div" v-model="transport_trans_Form.product_id">
                                                                    <div class="relative mt-2">
                                                                        <ComboboxInput
                                                                            class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                                <ul>
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
                                                                                </ul>
                                                                            </ComboboxOption>
                                                                        </ComboboxOptions>
                                                                    </div>
                                                                </Combobox>
                                                            </div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">No Units</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <input v-model="transport_load_Form.no_units_incoming" type="number"
                                                                   class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing units</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_incoming_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Package</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <Combobox as="div" v-model="transport_load_Form.packaging_incoming_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Source</dt>
                                                        <dd class="flex items-start gap-x-2">

                                                            <Combobox as="div" v-model="transport_load_Form.product_source_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Quantity / Grade</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <input v-model="transport_load_Form.product_grade_perc" type="text"
                                                                   class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Outgoing product</div>
                                                    <div class="text-sm font-light text-gray-900">(To Customer)</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">No Units</dt>
                                                        <dd class="text-gray-700">
                                                            <input v-model="transport_load_Form.no_units_outgoing" type="number"
                                                                   class="block w-48  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing units</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_outgoing_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Packaging</dt>
                                                        <dd class="flex items-start gap-x-2">

                                                            <Combobox as="div" v-model="transport_load_Form.packaging_outgoing_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Calculations</div>
                                                    <div class="text-sm font-light text-gray-900">(Weight & variances)</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Incoming Weight (Tons)</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div class="font-medium text-gray-900">{{selected_transaction.transport_finance.weight_ton_incoming}}</div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Outgoing Weight (Tons)</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div class="font-medium text-gray-900">{{selected_transaction.transport_finance.weight_ton_outgoing}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Weight Variance (Tons)</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div class="font-medium text-gray-900">{{selected_transaction.transport_finance.weight_ton_incoming-selected_transaction.transport_finance.weight_ton_outgoing}}</div>
                                                        </dd>
                                                    </div>
                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Product Notes</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="product_notes"
                                                            v-model="transport_trans_Form.product_notes"
                                                            :rows=12
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />

                                                        <InputError class="mt-2"
                                                                    :message="transport_trans_Form.errors.product_notes"/>

                                                    </div>

                                                </dl>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 2">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Customer details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>

                                                                <Combobox as="div" v-model="transport_trans_Form.customer_id">
                                                                    <div class="relative mt-2">
                                                                        <ComboboxInput
                                                                            class="w-80 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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

                                                                                <ul>
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
                                                                                </ul>
                                                                            </ComboboxOption>
                                                                        </ComboboxOptions>
                                                                    </div>
                                                                </Combobox>


                                                            </div>
                                                        </dd>

                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Customer Order number</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <input v-model="transport_job_Form.customer_order_number" type="text"
                                                                   class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Supplier loading number</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <input v-model="transport_job_Form.supplier_loading_number" type="text"
                                                                   class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                </dl>

                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Customer Account details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Delivery Address</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <div class="mt-2">
                                                                    <Combobox as="div" v-model="transport_load_Form.delivery_address_id">

                                                                        <div class="relative mt-2">
                                                                            <ComboboxInput
                                                                                class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                                    <ul>
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
                                                                                    </ul>
                                                                                </ComboboxOption>
                                                                            </ComboboxOptions>
                                                                        </div>
                                                                    </Combobox>
                                                                </div>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <div>
                                                            <div v-if="transport_load_Form.delivery_address_id">
                                                                <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                    Selected Delivery Address:</h3>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 1</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.delivery_address_id.line_1 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 2</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.delivery_address_id.line_2 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Line 3</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.delivery_address_id.line_3 }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Country</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.delivery_address_id.country }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                                <div class="flex justify-between gap-x-4 py-3">
                                                                    <dt class="text-gray-500">Code</dt>
                                                                    <dd class="text-gray-700">
                                                                        <div>
                                                                            {{ transport_load_Form.delivery_address_id.code }}
                                                                        </div>
                                                                    </dd>
                                                                </div>

                                                            </div>

                                                            <div v-else>
                                                                No customer addresses loaded...
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Payment Terms</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.customer.terms_of_payment.value}}</div>
                                                        </dd>
                                                    </div>


                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Invoice basis</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.customer.invoice_basis.value}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">VAT Exempt</dt>
                                                        <dd class="text-gray-700">
                                                            <div>
                                                                <check-icon v-if="selected_transaction.customer.is_vat_exempt" class="w-6 h-6 fill-green-300 " />
                                                                <XCircleIcon v-else class="w-6 h-6 fill-red-400 " />
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">VAT Certificate</dt>
                                                        <dd class="text-gray-700">
                                                            <div>
                                                                <check-icon v-if="selected_transaction.customer.is_vat_cert_received" class="w-6 h-6 fill-green-300 " />
                                                                <XCircleIcon v-else class="w-6 h-6 fill-red-400 " />

                                                            </div>
                                                        </dd>
                                                    </div>

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Product details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <dt class="text-gray-500">Product</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.product.name}}</div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing Units outgoing</dt>
                                                        <dd class="text-gray-700">
                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_outgoing_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Packaging</dt>
                                                        <dd class="text-gray-700">

                                                            <Combobox as="div" v-model="transport_load_Form.packaging_outgoing_id">
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500"> Selling Price / Unit</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.selling_price_per_unit)}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Selling Price / Ton</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.selling_price_per_ton)}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Total Selling Price</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.selling_price)}}</div>
                                                        </dd>
                                                    </div>

                                                </dl>
                                            </li>
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Customer notes</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="customer_notes"
                                                            v-model="transport_trans_Form.customer_notes"
                                                            :rows=12
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />

                                                        <InputError class="mt-2"
                                                                    :message="transport_trans_Form.errors.customer_notes"/>

                                                    </div>

                                                </dl>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 3">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Transport details</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <Combobox as="div" v-model="transport_trans_Form.transporter_id">

                                                                    <div class="relative mt-2">
                                                                        <ComboboxInput
                                                                            class="w-80 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                                <ul>
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
                                                                                </ul>
                                                                            </ComboboxOption>
                                                                        </ComboboxOptions>
                                                                    </div>
                                                                </Combobox>

                                                            </div>
                                                        </dd>

                                                    </div>



                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">

                                                            <div class="w-80">
                                                                <VueDatePicker
                                                                    v-model="transport_trans_Form.transport_date_earliest"
                                                                    :format="formatEarly"
                                                                    :teleport="true"></VueDatePicker>

                                                                <div class="ml-3 text-sm text-indigo-400">
                                                                    Transport date earliest
                                                                </div>
                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">

                                                            <div class="w-80">
                                                                <VueDatePicker
                                                                    v-model="transport_trans_Form.transport_date_latest"
                                                                    :format="formatLate"
                                                                    :teleport="true"></VueDatePicker>

                                                                <div class="ml-3 text-sm text-indigo-400">
                                                                    Transport date latest
                                                                </div>
                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">No loads</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <input v-model="transport_job_Form.number_loads" type="text"
                                                                   class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>


                                                </dl>

                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Driver & vehicles</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div v-if="viewDriverVehicleNewModal">

                                                        <driver-vehicle-modal-add
                                                            :show="viewDriverVehicleNewModal" @close="closeDriverVehicleModal"
                                                            :transport_trans_id="props.selected_transaction.id"
                                                            :transport_job_id="props.selected_transaction.transport_job.id"
                                                            :driver_vehicle="null"
                                                            :all_drivers="props.all_drivers"
                                                            :all_vehicles="props.all_vehicles"
                                                        />

                                                    </div>
                                                    <SecondaryButton @click="viewDriverNewVehicle()" class="m-6">
                                                        Add Driver Vehicle (+)
                                                    </SecondaryButton>

                                                    <div class="col-span-4">
                                                        <div
                                                            v-for="(driver_vehicle,index) in selected_transaction.transport_job.transport_driver_vehicle"
                                                            :key="driver_vehicle.id">
                                                            <div class="">
                                                                <div class="px-4 sm:px-0">
                                                                    <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">
                                                                        Driver vehicle {{ index + 1 }}</h3>
                                                                    <h3 class="text-base font-semibold leading-7 text-sm text-gray-400">
                                                                        Reference {{ driver_vehicle.id }}</h3>
                                                                </div>
                                                                <div class="mt-2 border-t border-gray-100">


                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Driver/Vehicle loading no</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            {{ driver_vehicle.driver_vehicle_loading_number }}
                                                                        </dd>
                                                                    </div>
                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Weighbridge upload weight</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            {{ driver_vehicle.weighbridge_upload_weight }}
                                                                        </dd>
                                                                    </div>
                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Weighbridge offload weight</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            {{ driver_vehicle.weighbridge_offload_weight }}
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Weighbridge variance</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_weighbridge_variance">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Cancelled</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_cancelled">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_cancelled && driver_vehicle.date_cancelled ? '(' + driver_vehicle.date_cancelled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_cancelled && driver_vehicle.date_cancelled ? '(' + driver_vehicle.date_cancelled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Loaded</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_loaded">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">On Road</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_onroad">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_loaded && driver_vehicle.date_loaded ? '(' + driver_vehicle.date_loaded + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Delivered</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_delivered">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_delivered && driver_vehicle.date_delivered ? '(' + driver_vehicle.date_delivered + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_delivered && driver_vehicle.date_delivered ? '(' + driver_vehicle.date_delivered + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>


                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Scheduled</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_transport_scheduled">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Paid</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_paid">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_transport_scheduled && driver_vehicle.date_scheduled ? '(' + driver_vehicle.date_scheduled + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Payment Overdue</dt>
                                                                        <dd class="flex items-start gap-x-2">
                                                                            <div v-if="driver_vehicle.is_payment_overdue">
                                                                                <icon name="tick-circle"
                                                                                      class=" w-6 h-6 fill-green-200"/>
                                                                                {{
                                                                                    driver_vehicle.is_payment_overdue && driver_vehicle.date_payment_overdue ? '(' + driver_vehicle.date_payment_overdue + ')' : ''
                                                                                }}
                                                                            </div>
                                                                            <div v-else>
                                                                                <icon name="cross-solid"
                                                                                      class="w-6 h-6 fill-red-500"/>
                                                                                {{
                                                                                    driver_vehicle.is_payment_overdue && driver_vehicle.date_payment_overdue ? '(' + driver_vehicle.date_payment_overdue + ')' : ''
                                                                                }}
                                                                            </div>
                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Traders notes</dt>
                                                                        <dd class="flex items-start gap-x-2">

                                                                            <div v-if="driver_vehicle.traders_notes">
                                                                                {{ driver_vehicle.traders_notes }}
                                                                            </div>
                                                                            <div v-else>
                                                                                none...
                                                                            </div>

                                                                        </dd>
                                                                    </div>

                                                                    <div class="flex justify-between gap-x-4 py-1">
                                                                        <dt class="text-gray-500">Operation alert notes</dt>
                                                                        <dd class="flex items-start gap-x-2">

                                                                            <div v-if="driver_vehicle.operations_alert_notes">
                                                                                {{ driver_vehicle.operations_alert_notes }}
                                                                            </div>
                                                                            <div v-else>
                                                                                none...
                                                                            </div>

                                                                        </dd>
                                                                    </div>


                                                                    <div>
                                                                        <div class="mt-3">
                                                                            <div class="">
                                                                                <div class="px-4 sm:px-0">
                                                                                    <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                                        Selected Driver</h3>
                                                                                </div>

                                                                                <div class="flex justify-between gap-x-4 py-1">
                                                                                    <dt class="text-gray-500">First name</dt>
                                                                                    <dd class="flex items-start gap-x-2">
                                                                                        {{ driver_vehicle.driver.first_name }}
                                                                                    </dd>
                                                                                </div>

                                                                                <div class="flex justify-between gap-x-4 py-1">
                                                                                    <dt class="text-gray-500">Last name</dt>
                                                                                    <dd class="flex items-start gap-x-2">
                                                                                        {{ driver_vehicle.driver.last_name }}
                                                                                    </dd>
                                                                                </div>

                                                                                <div class="flex justify-between gap-x-4 py-1">
                                                                                    <dt class="text-gray-500">Cell no</dt>
                                                                                    <dd class="flex items-start gap-x-2">
                                                                                        {{driver_vehicle.driver.cell_no}}
                                                                                    </dd>
                                                                                </div>

                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                    <div>
                                                                        <div class="mt-3">
                                                                            <div class="">
                                                                                <div class="px-4 sm:px-0">
                                                                                    <h3 class="text-base font-semibold leading-7 text-indigo-400">
                                                                                        Selected Vehicle</h3>
                                                                                </div>

                                                                                <div class="flex justify-between gap-x-4 py-1">
                                                                                    <dt class="text-gray-500">Registration number</dt>
                                                                                    <dd class="flex items-start gap-x-2">
                                                                                        {{ driver_vehicle.vehicle.reg_no }}
                                                                                    </dd>
                                                                                </div>

                                                                                <div class="flex justify-between gap-x-4 py-1">
                                                                                    <dt class="text-gray-500">Vehicle Type</dt>
                                                                                    <dd class="flex items-start gap-x-2">
                                                                                        {{ driver_vehicle.vehicle.vehicle_type.name }}
                                                                                    </dd>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>


                                                                </div>

                                                                <div class="mt-6 col-span-4">

                                                                    <div v-if="viewDriverVehicleModal">
                                                                        <DriverVehicleModal :show="viewDriverVehicleModal"
                                                                                            @close="closeDriverVehicleModal"
                                                                                            :transport_trans_id="props.selected_transaction.id"
                                                                                            :transport_job_id="props.selected_transaction.transport_job.id"
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

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Loading & Offloading instructions</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

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

                                                        </div>


                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="loading_instructions"
                                                            v-model="transport_job_Form.loading_instructions"
                                                            :rows=4
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />

                                                        <InputError class="mt-2"
                                                                    :message="transport_trans_Form.errors.customer_notes"/>

                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <div class="mt-2 flex col-span-4">

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


                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="offloading_instructions"
                                                            v-model="transport_job_Form.offloading_instructions"
                                                            :rows=4
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />


                                                    </div>
                                                </dl>


                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Transport notes</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="transport_notes"
                                                            v-model="transport_trans_Form.transport_notes"
                                                            :rows=12
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"

                                                        />

                                                        <InputError class="mt-2"
                                                                    :message="transport_trans_Form.errors.customer_notes"/>

                                                    </div>

                                                </dl>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 4">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Buying price</div>
                                                    <div class="text-sm font-light text-gray-900">(From Supplier)</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Supplier</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.supplier.last_legal_name}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Product</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.product.name}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Supply Packaging</dt>
                                                        <dd class="text-gray-700">
                                                            <Combobox as="div" v-model="transport_load_Form.packaging_incoming_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing Units</dt>
                                                        <dd class="text-gray-700">
                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_incoming_id">

                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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

                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">No Units</dt>
                                                        <dd class="text-gray-700">
                                                            {{selected_transaction.transport_load.no_units_incoming}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Supply Weight (tons)</dt>
                                                        <dd class="text-gray-700">
                                                            {{selected_transaction.transport_load.weight_ton_incoming}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Cost Price / Unit</dt>
                                                        <dd class="text-gray-700">
                                                            <input v-model="transport_finance_Form.cost_price_per_unit" type="number"
                                                                   class="block w-48  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Total Supplier Cost</dt>
                                                        <dd class="text-gray-700">
                                                            {{NiceNumber(selected_transaction.transport_finance.total_cost_price)}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Cost Price / Ton</dt>
                                                        <dd class="text-gray-700">
                                                            {{NiceNumber(selected_transaction.transport_finance.cost_price_per_ton)}}
                                                        </dd>
                                                    </div>




                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Transport costs</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transport rate basis</dt>
                                                        <dd class="text-gray-700">
                                                            <div>
                                                                <select v-model="transport_finance_Form.transport_rate_basis_id"
                                                                        class="mt-2 block w-48 rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                                    <option v-for="n in props.all_transport_rates" :key="n.id" :value="n.id">
                                                                        {{ n.name }}
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transport rate</dt>
                                                        <dd class="text-gray-700">
                                                            <div>
                                                                <input v-model="transport_finance_Form.transport_rate" type="number"
                                                                       class="block w-48  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transport rate / Ton</dt>
                                                        <dd class="text-gray-700">
                                                            {{NiceNumber(selected_transaction.transport_finance.transport_rate_per_ton)}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transport costs</dt>
                                                        <dd class="text-gray-700">
                                                            {{NiceNumber(selected_transaction.transport_finance.transport_cost)}}
                                                        </dd>
                                                    </div>



                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Load Insurance per ton</dt>
                                                        <dd class="text-gray-700">
                                                            {{NiceNumber(selected_transaction.transport_finance.load_insurance_per_ton)}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transport cost in price</dt>
                                                        <dd class="text-gray-700">

                                                            <SwitchGroup as="div" class="flex m-2 items-center">
                                                                <Switch v-model="transport_job_Form.is_transport_costs_inc_price"
                                                                        :class="[transport_job_Form.is_transport_costs_inc_price ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                        <span aria-hidden="true"
                                                              :class="[transport_job_Form.is_transport_costs_inc_price ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                </Switch>

                                                            </SwitchGroup>

                                                        </dd>
                                                    </div>


                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Selling Price</div>
                                                    <div class="text-sm font-light text-gray-900">(To Customer)</div>
                                                </div>
                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <dt class="text-gray-500">Customer</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.customer.last_legal_name}}</div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <dt class="text-gray-500">Product</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{selected_transaction.product.name}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Selling Packaging</dt>
                                                        <dd class="flex items-start gap-x-2">

                                                            <Combobox as="div" v-model="transport_load_Form.packaging_outgoing_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Billing units</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <Combobox as="div" v-model="transport_load_Form.billing_units_outgoing_id">
                                                                <div class="relative mt-2">
                                                                    <ComboboxInput
                                                                        class="w-48 rounded-md border-0 bg-white py-1.5 pl-3 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
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
                                                                            <ul>
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
                                                                            </ul>
                                                                        </ComboboxOption>
                                                                    </ComboboxOptions>
                                                                </div>
                                                            </Combobox>


                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">No Units</dt>
                                                        <dd class="text-gray-700">
                                                            {{selected_transaction.transport_load.no_units_outgoing}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Selling Weight (tons)</dt>
                                                        <dd class="text-gray-700">
                                                            {{selected_transaction.transport_load.weight_ton_outgoing}}
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Selling Price / Unit</dt>
                                                        <dd class="text-gray-700">
                                                            <input v-model="transport_finance_Form.selling_price_per_unit" type="number"
                                                                   class="block w-48  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Total Selling Price</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.selling_price)}}</div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Selling Price / Ton</dt>
                                                        <dd class="text-gray-700">
                                                            <div>{{NiceNumber(selected_transaction.transport_finance.selling_price_per_ton)}}</div>
                                                        </dd>
                                                    </div>


                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Margin Calculation</div>
                                                </div>

                                                <div>

                                                    <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                        <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">Additional costs:</h3>

                                                        <div class="flex justify-between gap-x-4 py-3">

                                                            <dt class="text-gray-500">
                                                                <input v-model="transport_finance_Form.additional_cost_desc_1"
                                                                       placeholder="Description..." type="text"
                                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                            </dt>
                                                            <dd class="text-gray-700">
                                                                <div>
                                                                    <input v-model="transport_finance_Form.additional_cost_1" type="number"
                                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">

                                                            <dt class="text-gray-500">
                                                                <input v-model="transport_finance_Form.additional_cost_desc_1"
                                                                       placeholder="Description..." type="text"
                                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                            </dt>
                                                            <dd class="text-gray-700">
                                                                <div>
                                                                    <input v-model="transport_finance_Form.additional_cost_1" type="number"
                                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">

                                                            <dt class="text-gray-500">
                                                                <input v-model="transport_finance_Form.additional_cost_desc_1"
                                                                       placeholder="Description..." type="text"
                                                                       class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                            </dt>
                                                            <dd class="text-gray-700">
                                                                <div>
                                                                    <input v-model="transport_finance_Form.additional_cost_1" type="number"
                                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div>
                                                            <h3 class="text-base font-semibold mt-2 leading-7 text-indigo-400">Adjusted GP:</h3>
                                                            <div class="flex justify-between gap-x-4 py-3">

                                                                <dt class="text-gray-500">
                                                                    <input v-model="transport_finance_Form.adjusted_gp_notes"
                                                                           placeholder="Description..." type="text"
                                                                           class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                                </dt>
                                                                <dd class="text-gray-700">
                                                                    <div>
                                                                        <input v-model="transport_finance_Form.adjusted_gp" type="number"
                                                                               class="block w-full  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>
                                                                    </div>
                                                                </dd>
                                                            </div>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">

                                                            <dt class="text-gray-500">Selling Price</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.selling_price)}}</div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">

                                                            <dt class="text-gray-500">Cost Price</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.cost_price)}}</div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Transport Cost</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.transport_cost)}}</div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Total Cost Price</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.total_cost_price)}}</div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">GP</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.gross_profit)}}</div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">GP / Ton</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{NiceNumber(selected_transaction.transport_finance.gross_profit_per_ton)}}</div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">GP %</dt>
                                                            <dd class="text-gray-700">
                                                                <div>{{selected_transaction.transport_finance.gross_profit_percent}}</div>
                                                            </dd>
                                                        </div>

                                                    </dl>


                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 5">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8" role="list">
                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
                                                    <div class="text-sm font-light text-gray-900">(State)</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Phase</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <div class="">
                                                                    <select v-model="transport_invoice_Form.status_id"
                                                                            class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                                                        <option v-for="n in props.all_invoice_statuses" :key="n.id" :value="n.id">
                                                                            {{n.name}}
                                                                        </option>
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </dd>
                                                    </div>
                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Active</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_invoice_Form.is_active"
                                                                            :class="[transport_invoice_Form.is_active ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_active ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>
                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Printed</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_invoice_Form.is_printed"
                                                                            :class="[transport_invoice_Form.is_printed ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_printed? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>
                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Invoiced</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_invoice_Form.is_invoiced"
                                                                            :class="[transport_invoice_Form.is_invoiced ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_invoiced ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>
                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Paid</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_invoice_Form.is_invoice_paid"
                                                                            :class="[transport_invoice_Form.is_invoice_paid ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                <span aria-hidden="true"
                                                      :class="[transport_invoice_Form.is_invoice_paid ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>
                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>


                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
                                                    <div class="text-sm font-light text-gray-900">(Details)</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">

                                                            <div class="w-80">
                                                                <VueDatePicker style="width: 250px;"
                                                                               v-model="transport_invoice_Form.invoice_date"
                                                                               :format="formatInvoiceDate"
                                                                               :teleport="true"></VueDatePicker>

                                                                <div class="ml-3 text-sm text-indigo-400">
                                                                    Invoice date
                                                                </div>
                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">

                                                            <div class="w-80">
                                                                <VueDatePicker style="width: 250px;"
                                                                               v-model="transport_invoice_Form.invoice_pay_by_date"
                                                                               :format="formatInvoicePayByDay"
                                                                               :teleport="true"></VueDatePicker>

                                                                <div class="ml-3 text-sm text-indigo-400">
                                                                    Invoice pay by date
                                                                </div>
                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dd class="flex items-start gap-x-2">

                                                            <div class="w-80">
                                                                <VueDatePicker style="width: 250px;"
                                                                               v-model="transport_invoice_Form.invoice_paid_date"
                                                                               :format="formatInvoicePdDay"
                                                                               :teleport="true"></VueDatePicker>

                                                                <div class="ml-3 text-sm text-indigo-400">
                                                                   Invoice paid date
                                                                </div>
                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Invoice No</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <input v-model="transport_invoice_Form.invoice_no" type="text"
                                                                       class="block w-48  rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <AreaInput
                                                            v-model="transport_invoice_Form.notes"
                                                            :rows=4
                                                            placeholder="Optional comments..."
                                                            type="text"
                                                            class="mt-1 block w-full"
                                                        />
                                                    </div>



                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Invoice</div>
                                                    <div class="text-sm font-light text-gray-900">(Finance)</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Invoice amount</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <input v-model="transport_invoice_Form.invoice_amount" type="number"
                                                                       class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Invoice amount paid</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <input v-model="transport_invoice_Form.invoice_amount_paid" type="number"
                                                                       class="block w-48 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"/>

                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">cost_price</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                {{NiceNumber(selected_transaction.transport_finance.cost_price)}}
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">total_cost_price</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                {{NiceNumber(selected_transaction.transport_finance.total_cost_price)}}
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">gross_profit</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                {{NiceNumber(selected_transaction.transport_finance.gross_profit)}}
                                                            </div>
                                                        </dd>
                                                    </div>



                                                </dl>
                                            </li>

                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 6">

                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-3 xl:gap-x-8" role="list">
                                            <li v-if="selected_transaction.contract_type_id ===4"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Approvals</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div class="mb-2">
                                                        <div v-if="selected_transaction.deal_ticket.is_active" class=" mt-3">
                                                            <div class="text-green-400">Deal Ticket is Active </div>
                                                            <div class=" text-indigo-400">{{ selected_transaction.contract_type.name}}{{ selected_transaction.id}}</div>
                                                            <div v-if="selected_transaction.contract_type_id ===4" class="mb-2 text-gray-400">OLD {{ selected_transaction.contract_type.name}}{{ selected_transaction.deal_ticket.old_id}}</div>
                                                            <div v-else class="mb-2 text-gray-400">{{ selected_transaction.contract_type.name}}{{ selected_transaction.old_id}}</div>
                                                        </div>

                                                        <div v-else class="text-red-400 mt-3">
                                                            Deal Ticket Not Active
                                                        </div>
                                                    </div>

                                                    <div>

                                                        <div class="col-span-4">
                                                            <div class="">

                                                                <div class="">
                                                                    <div class="flex-row text-indigo-400 text-lg mb-2">
                                                                        <span>Trade Rules </span>
                                                                    </div>
                                                                    <div class="mt-2 flow-root">
                                                                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                                                                <table class="min-w-full divide-y divide-gray-300">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Rule</th>
                                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Role</th>
                                                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Approved</th>

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
                                                                                                        <span><check-icon class="w-6 h-6 fill-green-300 mr-3" />  </span>
                                                                                                        <div><span>{{m.user.name}}</span></div>
                                                                                                    </div>
                                                                                                </li>

                                                                                                <div v-else class="flex">
                                                                                                    <span><XCircleIcon class="w-6 h-6 fill-red-400 mr-3" />  </span> <span>None...</span>
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

                                                        <div class="col-span-4">
                                                            <div class="">

                                                                <div class="">
                                                                    <div class="flex-row text-indigo-400 text-lg mb-2">
                                                                        <span>Trade Operation Rules </span>
                                                                    </div>

                                                                    <div class="sm:flex sm:items-center">
                                                                    </div>
                                                                    <div class="mt-2 flow-root">
                                                                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                                                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                                                                <table class="min-w-full divide-y divide-gray-300">
                                                                                    <thead>
                                                                                    <tr>
                                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Rule</th>
                                                                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Role</th>
                                                                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Approved</th>

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
                                                                                                        <span><check-icon class="w-6 h-6 fill-green-300 mr-3" />  </span> <span> {{m.user.name}} </span>
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

                                                    <div class="flex-row text-indigo-400 text-lg mb-2">
                                                        <span>Deal Ticket</span>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Is Active</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <check-icon v-if="selected_transaction.deal_ticket.is_active" class="w-6 h-6 fill-green-300 " />
                                                                <XCircleIcon v-else class="w-6 h-6 fill-red-400 " />
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Is Approved</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <check-icon v-if="selected_transaction.deal_ticket.is_approved" class="w-6 h-6 fill-green-300 " />
                                                                <XCircleIcon v-else class="w-6 h-6 fill-red-400 " />
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Is Printed</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <check-icon v-if="selected_transaction.deal_ticket.is_printed" class="w-6 h-6 fill-green-300 " />
                                                                <XCircleIcon v-else class="w-6 h-6 fill-red-400 " />
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="mt-2">
                                                        <SecondaryButton @click="createApproval" class="m-1">
                                                            Approve
                                                        </SecondaryButton>

                                                        <SecondaryButton @click="createActivation" class="m-1">
                                                            Activate
                                                        </SecondaryButton>

                                                        <a :href="'/pdf_report/deal_ticket_view/' + props.selected_transaction.id" target="_blank"
                                                           class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                         View (working doc)
                                                        </a>
                                                    </div>


                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Operational Alerts</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

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


                                                                <div class="col-span-4 mt-1">

                                                                    <SecondaryButton @click="createStatus" class="">
                                                                        Add Status (+)
                                                                    </SecondaryButton>

                                                                </div>


                                                                <div class="col-span-6">
                                                                    <div>

                                                                        <div class="flex">

                                                                            <ul class="w-full">

                                                                                <li v-for="n in selected_transaction.transport_status" :key="n.id" :value="n.id"
                                                                                    class="w-full border-b-2 border-neutral-100 border-opacity-100 py-4 dark:border-opacity-50">

                                                                                    <div class="flex">

                                                                                        <div class="flex-auto font-bold w-2/3">

                                                                                            <div class="rounded-md bg-yellow-50 p-4">
                                                                                                <div class="flex">
                                                                                                    <div class="flex-shrink-0">
                                                                                                        <ExclamationTriangleIcon class="h-5 w-5 text-yellow-400" aria-hidden="true" />
                                                                                                    </div>
                                                                                                    <div class="ml-3">
                                                                                                        <h3 class="text-lg uppercase font-medium text-yellow-800">{{n.status_entity.entity}}</h3>
                                                                                                        <div class="mt-2 text-sm text-yellow-700">

                                                                                                            <p class="uppercase">{{n.status_type.type}}</p>
                                                                                                            <p class="italic text-sm">{{n.created_at}}</p>

                                                                                                        </div>
                                                                                                    </div>

                                                                                                    <div class="ml-auto pl-3">
                                                                                                        <div class="-mx-1.5 -my-1.5">
                                                                                                            <button type="button" class="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50">
                                                                                                                <span class="sr-only">Dismiss</span>
                                                                                                                <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                                                                                                            </button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>


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

                                                </dl>
                                            </li>

                                            <li  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Operations</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Contract type</dt>
                                                        <dd class="flex items-start gap-x-2">

                                                            <div>
                                                                <Combobox as="div" v-model="transport_trans_Form.contract_type_id">
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
                                                                                <ul>
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
                                                                                </ul>
                                                                            </ComboboxOption>
                                                                        </ComboboxOptions>
                                                                    </div>
                                                                </Combobox>

                                                            </div>

                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Include in calculations</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_trans_Form.include_in_calculations"
                                                                            :class="[transport_trans_Form.include_in_calculations ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                    <span aria-hidden="true"
                                                          :class="[transport_trans_Form.include_in_calculations ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>
                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Transaction done</dt>
                                                        <dd class="flex items-start gap-x-2">

                                                            <div>
                                                                <SwitchGroup as="div" class="flex m-2 items-center">
                                                                    <Switch v-model="transport_trans_Form.is_transaction_done"
                                                                            :class="[transport_trans_Form.is_transaction_done ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2']">
                                                                <span aria-hidden="true"
                                                                      :class="[transport_trans_Form.is_transaction_done ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']"/>
                                                                    </Switch>

                                                                </SwitchGroup>
                                                            </div>
                                                        </dd>
                                                    </div>

                                                    <div class="text-lg mb-2 text-indigo-400">Process notes</div>

                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="process_notes"
                                                            v-model="transport_trans_Form.process_notes"
                                                            :rows=4
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"
                                                        />



                                                    </div>

                                                    <div class="text-lg mb-2 text-indigo-400">Traders notes</div>

                                                    <div class="flex justify-between gap-x-4 py-3">

                                                        <AreaInput
                                                            id="traders_notes"
                                                            v-model="transport_trans_Form.traders_notes"
                                                            :rows=4
                                                            placeholder="Optional notes..."
                                                            type="text"
                                                            class="mt-1 block w-1/3"
                                                        />



                                                    </div>





                                                </dl>
                                            </li>

                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 7">

                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-2 xl:gap-x-8" role="list">
                                            <li v-if="selected_transaction.contract_type_id ===4"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Linked Contracts</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div>

                                                        <div class="">

                                                            <div class="">

                                                                <div v-if="selected_transaction.contract_type_id === 1">
                                                                    Unallocated
                                                                </div>

                                                                <div v-if="selected_transaction.contract_type_id === 2">

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

                                                                <div v-if="selected_transaction.contract_type_id === 3">
                                                                    SC
                                                                </div>

                                                                <div v-if="selected_transaction.contract_type_id === 4">

                                                                    <div class="text-indigo-400 font-bold">
                                                                        MQ
                                                                    </div>

                                                                    <SecondaryButton class="m-1 mt-3" @click="viewContractLink">
                                                                        Link MQ
                                                                    </SecondaryButton>

                                                                    <ContractLinkModal
                                                                        :show="viewContractLinkModal"
                                                                        @close="closeContractLink"
                                                                        :mq_trans_id="selected_transaction.id"
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


                                                </dl>
                                            </li>


                                        </ul>
                                    </div>

                                    <div v-if="selectedTabId === 8">
                                        <ul class="grid grid-cols-1 gap-x-6 gap-y-8 lg:grid-cols-4 xl:gap-x-8" role="list">
                                            <li v-if="selected_transaction.contract_type_id === 4" class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Deal Ticket</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div v-if="deal_ticket.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                        <dt class="text-gray-500">Working Document</dt>
                                                        <dd class="flex items-start gap-x-2">
                                                            <a :href="'/pdf_report/deal_ticket_view/' + props.selected_transaction.id" target="_blank"
                                                               class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                View
                                                            </a>
                                                        </dd>
                                                    </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="createFinalDealTicket">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final

                                                            </dt>

                                                            <dd class="flex items-start gap-x-2">



                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>


                                                            </dd>


                                                        </div>
                                                    </div>

                                                    <div class="text-red-400" v-else>
                                                        <p class="font-bold">Deal Ticket Not Active</p>
                                                        <p>(Activate via process control)</p>

                                                    </div>


                                                </dl>


                                            </li>
                                            <li v-if="selected_transaction.contract_type_id === 4"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Transport Order Confirmation</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">

                                                    <div v-if="transport_order.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Confirmation Sent</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="transport_order.is_to_sent">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="sendTransportOrder">
                                                                            Sent
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Received</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="transport_order.is_to_received">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="receiveTransportOrder">
                                                                            Received
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Working Document</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <a :href="'/pdf_report/transport_order_confirmation_view/' + props.selected_transaction.id" target="_blank"
                                                                   class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                    View
                                                                </a>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final</dt>

                                                            <dd class="flex items-start gap-x-2">

                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>


                                                            </dd>


                                                        </div>
                                                    </div>

                                                    <div v-else>

                                                        <p class="text-red-400 font-bold">Transport order Not Active</p>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Activate Transport Order</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="activateTransportOrder">
                                                                    Activate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                    </div>


                                                </dl>
                                            </li>

                                            <li v-if="selected_transaction.contract_type_id === 2"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Purchase Order</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div v-if="purchase_order.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Confirmation Sent</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="purchase_order.is_po_sent">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="sendPurchaseOrder">
                                                                            Sent
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Received</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="purchase_order.is_po_received">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="receivePurchaseOrder">
                                                                            Received
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Working Document</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <a :href="'/pdf_report/purchase_order_view/' + props.selected_transaction.id" target="_blank"
                                                                   class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                    View
                                                                </a>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final</dt>

                                                            <dd class="flex items-start gap-x-2">

                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>


                                                            </dd>


                                                        </div>
                                                    </div>

                                                    <div v-else>

                                                        <p class="text-red-400 font-bold">Purchase order Not Active</p>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Activate Purchase Order</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="activatePurchaseOrder">
                                                                    Activate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                    </div>



                                                </dl>
                                            </li>
                                            <li v-if="selected_transaction.contract_type_id === 2"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Purchase Order Confirmation</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div v-if="purchase_order.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Working Document</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <a :href="'/pdf_report/purchase_order_confirmation_view/' + props.selected_transaction.id" target="_blank"
                                                                   class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                    View
                                                                </a>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final</dt>

                                                            <dd class="flex items-start gap-x-2">

                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>

                                                            </dd>
                                                        </div>
                                                    </div>

                                                    <div v-else>
                                                        Sales order Not Active
                                                    </div>

                                                </dl>
                                            </li>

                                            <li v-if="selected_transaction.contract_type_id === 3"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Sales Order</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">
                                                    <div v-if="sales_order.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Confirmation Sent</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="sales_order.is_sa_conf_sent">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="sendSalesOrder">
                                                                            Sent
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Received</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <div>
                                                                    <div v-if="sales_order.is_sa_conf_received">
                                                                        <p>
                                                                            <check-icon class="h-5 h-5"/>
                                                                        </p>
                                                                    </div>
                                                                    <div v-else>
                                                                        <SecondaryButton @click="receiveSalesOrder">
                                                                            Received
                                                                        </SecondaryButton>
                                                                    </div>
                                                                </div>
                                                            </dd>
                                                        </div>

                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Working Document</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <a :href="'/pdf_report/sales_order_view/' + props.selected_transaction.id" target="_blank"
                                                                   class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                    View
                                                                </a>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final</dt>

                                                            <dd class="flex items-start gap-x-2">

                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>


                                                            </dd>


                                                        </div>
                                                    </div>

                                                    <div v-else>

                                                        <p class="text-red-400 font-bold">Sales order Not Active</p>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Activate Sales Order</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="activateSalesOrder">
                                                                    Activate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                    </div>



                                                </dl>
                                            </li>
                                            <li v-if="selected_transaction.contract_type_id === 3"  class="overflow-hidden rounded-xl border border-gray-200">
                                                <div class="flex items-center gap-x-4 border-b border-gray-900/5 bg-gray-50 p-6">
                                                    <div class="text-sm font-medium leading-6 text-gray-900">Sales Order Confirmation</div>
                                                </div>

                                                <dl class="-my-3 divide-y divide-gray-100 px-6 py-4 text-sm leading-6">


                                                    <div v-if="sales_order.is_active">
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Working Document</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <a :href="'/pdf_report/sales_order_confirmation_view/' + props.selected_transaction.id" target="_blank"
                                                                   class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                    View
                                                                </a>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Generate Final</dt>
                                                            <dd class="flex items-start gap-x-2">
                                                                <SecondaryButton @click="">
                                                                    Generate
                                                                </SecondaryButton>
                                                            </dd>
                                                        </div>
                                                        <div class="flex justify-between gap-x-4 py-3">
                                                            <dt class="text-gray-500">Download Final</dt>

                                                            <dd class="flex items-start gap-x-2">

                                                                <div v-if="deal_ticket.report_path">
                                                                    <a :href="route('pdf_report.deal_ticket_final.download',deal_ticket.report_path)" target="_blank"
                                                                       class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                                                        Download
                                                                    </a>
                                                                </div>

                                                                <div v-else>
                                                                    <p>Not generated</p>
                                                                </div>

                                                            </dd>
                                                        </div>
                                                    </div>

                                                    <div v-else>
                                                        Sales order Not Active
                                                    </div>

                                                </dl>
                                            </li>


                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </div>



        </div>
    </AppLayout>
</template>
