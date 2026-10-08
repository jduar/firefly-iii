import Get from "../../../api/model/transaction-template/get.js";

export function loadTransactionTemplates() {
    let params = {
        page: 1,
        limit: 1337,
    };
    let getter = new Get();
    return getter.list(params).then((response) => {
        let returnData = [];
        for (let i in response.data.data) {
            if (Object.hasOwn(response.data.data, i)) {
                let current = response.data.data[i];
                let attributes = current.attributes;
                let obj = {
                    id: current.id,
                    name: attributes.name,
                    transaction_description: attributes.transaction_description,
                    source_account_id: attributes.source_account_id,
                    source_account_name: attributes.source_account_name,
                    destination_account_id: attributes.destination_account_id,
                    destination_account_name: attributes.destination_account_name,
                    budget_id: attributes.budget_id,
                    category_name: attributes.category_name,
                    tags: attributes.tags,
                    notes: attributes.notes,
                };
                returnData.push(obj);
            }
        }
        return returnData;
    });
}
