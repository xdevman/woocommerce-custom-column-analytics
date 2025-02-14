import './index.scss';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

const addTableColumn = (reportTableData) => {
    if ('orders' !== reportTableData.endpoint) {
        return reportTableData;
    }

    const newHeaders = [
        ...reportTableData.headers,
        {
            label: __('Payment Method', 'woocommerce-custom-column-analytics'),
            key: 'payment_method',
            required: false,
        },
        {
            label: __('Phone Number', 'woocommerce-custom-column-analytics'),
            key: 'phone_number',
            required: false,
        },
        {
            label: __('ID Card', 'woocommerce-custom-column-analytics'),
            key: 'id_card',
            required: false,
        },
    ];

    const newRows = reportTableData.rows.map((row, index) => {
        const item = reportTableData.items.data[index];
        const newRow = [
            ...row,
            {
                display: item.payment_method || __('N/A', 'woocommerce-custom-column-analytics'),
                value: item.payment_method || '',
            },
            {
                display: item.billing_phone || __('No Value', 'woocommerce-custom-column-analytics'),
                value: item.billing_phone || '',
            },
            {
                display: item.billing_idcard || __('No Value', 'woocommerce-custom-column-analytics'),
                value: item.billing_idcard || '',
            },
        ];
        return newRow;
    });

    reportTableData.headers = newHeaders;
    reportTableData.rows = newRows;

    return reportTableData;
};

addFilter('woocommerce_admin_report_table', 'woocommerce-custom-column-analytics', addTableColumn);