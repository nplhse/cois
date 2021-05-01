<template>
    <div>
        <h1>Hello, world.</h1>

        <PieChart :data="gender">

        </PieChart>
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
        gender: [
          { gender: "Male", value: 47 },
          { gender: "Female", value: 53 },
          { gender: "Other", value: 0 },
        ]
      };
    },
};
</script>

<style scoped></style>
