<template>
    <div>
        <h1>Hello, world.</h1>

        <b-row>
          <b-col cols="8">

          </b-col>
          <b-col cols="4">
            <PieChart :data="this.data" />
          </b-col>
        </b-row>
    </div>
</template>

<script>
import PieChart from "../components/PieChart";

export default {
    name: "Statistics",
    components: {
      PieChart,
    },
    data() {
      return {
        data: [
          {'gender': 'male', 'value': 47},
          {'gender': 'female', 'value': 53},
          {'gender': 'other', 'value': 0},
        ],
      };
    },
};
</script>

<style scoped></style>
