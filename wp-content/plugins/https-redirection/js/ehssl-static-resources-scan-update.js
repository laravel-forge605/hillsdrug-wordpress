/* global ehssl_non_https_resources_scan_update_js_data */

document.addEventListener('DOMContentLoaded', function () {
    const {ajaxUrl, resultsNonce, texts} = ehssl_non_https_resources_scan_update_js_data;
    const scanForm = document.getElementById('ehssl_non_https_resources_scan_form');
    const scanBtn = document.getElementById('ehssl_non_https_resources_scan_btn');
    const resultsBox = document.getElementById('ehssl_scan_results');

    scanForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        scanBtn.disabled = true;
        let scanBtnText = scanBtn.textContent;
        scanBtn.textContent = texts.scan_btn_loading;
        scanBtnText = texts.rescan_btn;

        const checked_post_types = scanForm.querySelectorAll('input[name="ehssl_post_types[]"]:checked');
        const checked_other_tables = scanForm.querySelectorAll('input[name="ehssl_other_tables[]"]:checked');

        if (!checked_post_types.length && !checked_other_tables.length) {
            alert(texts.pls_select_an_item);
            scanBtn.disabled = false;
            scanBtn.innerText = scanBtnText;
            return;
        }

        resultsBox.innerHTML = '';

        const formData = new FormData(e.target);
        formData.append('action', 'ehssl_non_https_resources_scan');
        formData.append('offset', 0);
        formData.append('total', JSON.stringify([]));

        const onComplete = async(resp) => {
            // console.log('processBatchScan onComplete:', resp); // Debug Purpose Only

            if (resp.success) {
                // Display the rendered table.
                await getScannedResourcesTable((tableHTML) => {
                    resultsBox.innerHTML = tableHTML;
                })
            }

            scanBtn.disabled = false;
            scanBtn.textContent = scanBtnText;
        }

        const onError = (response) => {
            // return response;
            let resp_msg;

            if ( response?.message !== undefined) {
                resp_msg = response.message;
            } else if ( response?.data?.message !== undefined) {
                resp_msg = response.data.message;
            } else {
                resp_msg = 'Something went wrong';
            }

            console.log(resp_msg, response);
            alert(resp_msg);
            scanBtn.disabled = false;
            scanBtn.innerText = scanBtnText;
        }

        await processBatchScan(formData, onComplete, onError);
    });

    async function processBatchScan(formData, onComplete, onError) {
        try {
            let response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })

            response = await response.json();

            const {success, data} = response;
            if (!success) {
                onError(response);
                return;
            }

            // console.log('processBatchScan response:', response); // Debug Purpose Only

            if (!data.completed) {
                formData.append('total', JSON.stringify(data.total));
                formData.append('offset', data.next_offset);

                await processBatchScan(formData, onComplete, onError);
            } else {
                onComplete(response);
            }

        } catch (error) {
            onError(error);
        }
    }

    async function getScannedResourcesTable(cb) {
        try {
            const currentParams = new URLSearchParams(window.location.search);
            const currentPage = currentParams.get('page');
            const currentTab = currentParams.get('tab');

            const url = new URL(ajaxUrl);
            url.searchParams.append('action', 'ehssl_get_scanned_resources_table');
            url.searchParams.append('nonce', resultsNonce);
            url.searchParams.append('page', currentPage);
            url.searchParams.append('tab', currentTab);

            let response = await fetch(url, {
                method: 'GET',
            })

            response = await response.text();

            cb(response);
        } catch (error) {
            alert(error.message);
        }
    }

    /**
     * AJAX pagination
     */
    document.addEventListener('click', function (e) {
        const navLink = e.target.closest('.tablenav-pages a');
        if (!navLink) {
            return;
        }
        e.preventDefault();

        const currentParams = new URLSearchParams(window.location.search);

        const url = new URL(ajaxUrl);
        url.searchParams.append('action', 'ehssl_load_static_resources_table_page');
        url.searchParams.append('nonce', resultsNonce);
        url.searchParams.append('page', currentParams.get('page'));
        url.searchParams.append('tab', currentParams.get('tab'));

        const navLinkUrl = new URL(navLink.href);
        const paged = navLinkUrl.searchParams.get('paged') || 1;
        url.searchParams.append('paged', paged);

        fetch(url, {
            method: 'GET',
        })
            .then(response => response.text())
            .then(html => {
                resultsBox.innerHTML = html;
            });
    });

    /**
     * Update URLs action.
     */
    document.addEventListener('click', function (e) {
        if (e.target.matches('#doaction, #doaction2')) {
            handle_update_urls(e);
        }

        else if (e.target.matches('#ehssl_update_all_found_http_urls')) {
            handle_update_urls(e, true);
        }
    });


    function handle_update_urls(e, update_all = false){
        e.preventDefault();

        const actionBtn = e.target;
        const actionBtnText = actionBtn.textContent;

        const nonce_input = document.querySelector('input[name="ehssl_update_all_http_urls_nonce"]');
        const nonce = nonce_input.value || '';

        const formData = new FormData();
        formData.append('action', 'ehssl_update_http_urls');
        formData.append('nonce', nonce);
        formData.append('offset', 0);

        const onComplete = (resp) => {
            getScannedResourcesTable((tableHTML) => {
                resultsBox.innerHTML = tableHTML;

                disableButtons(false);
            })
        }

        if (update_all) {
            actionBtn.textContent = texts.update_btn_loading;
        } else {
            const bulkSelect = actionBtn.id === 'doaction'
                ? document.getElementById('bulk-action-selector-top')
                : document.getElementById('bulk-action-selector-bottom');

            if (!bulkSelect || bulkSelect.value !== 'update_to_https') {
                alert('Please select an action!');
                return;
            }

            const selected_ids = [];

            const checkboxes = document.querySelectorAll('input[name="ehssl_non_https_resources_scan_ids[]"]:checked');
            checkboxes?.forEach(function (el) {
                selected_ids.push(el.value);
            });

            if (!selected_ids.length) {
                alert('Please select at least one item!');
                return;
            }

            formData.append('selected_ids', JSON.stringify(selected_ids));
        }

        if (!confirm(texts.confirm_update)) {
            disableButtons(false);
            actionBtn.textContent = actionBtnText;
            return;
        }

        disableButtons();

        processBatchUpdate(formData, onComplete);
    }

    function disableButtons(value = true){
        document.querySelectorAll('#ehssl_update_all_found_http_urls, #doaction, #doaction2').forEach((btn) => {
            btn.disabled = value;
        });
    }

    async function processBatchUpdate(formData, onComplete) {
        try {
            let response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
            })

            response = await response.json();

            const {success, data} = response;
            if (!success) {
                alert('Something went wrong');
                console.log('Something went wrong', response);
                return;
            }

            // console.log(`${data.processed}/${data.total}`); // Debug purpose only.

            if (!data.completed) {
                formData.append('offset', data.next_offset);
                await processBatchUpdate(formData, onComplete);
            } else {
                onComplete(response);
            }
        } catch (error) {
            alert(error.message);
        }
    }
});