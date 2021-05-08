<template>
    <div>
        <b-table
            id="rmi-table"
            striped
            hover
            :items="this.items"
            :fields="fields"
            :loading="loading"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :label-sort-asc="null"
            :label-sort-desc="null"
            sort-icon-right
        >
            <template #table-busy>
                <div class="text-center my-2">
                    <b-spinner class="align-middle" />
                    <strong>Loading...</strong>
                </div>
            </template>
        </b-table>

        <div>
            Sorting By: <b>{{ this.sortBy }}</b
            >, Sort Direction:
            <b>{{ this.sortDesc ? "Descending" : "Ascending" }}</b>
        </div>
    </div>
</template>

<script>
export default {
    name: "Statistics",
    data() {
        return {
            data: [],
            items: [],
            sortBy: "count",
            sortDesc: true,
            loading: true,
            fields: [
                { key: "RMI", label: "RMI", sortable: true },
                { key: "PZCText", label: "Caption", sortable: true },
                { key: "count", label: "Count", sortable: true },
            ],
        };
    },
    created() {
        let items = JSON.parse(document.getElementById("pageData").innerHTML);

        this.items = Object.keys(items).map((key) => {
            return items[key];
        });

        this.totalItems;
    },
};
</script>

<style scoped></style>
