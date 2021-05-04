<template>
    <div>
        <h1>Hello, world.</h1>

        <b-row>
          <b-col cols="8">
            <AgeDistributionPanel />
          </b-col>
          <b-col cols="4">
            <GenderDistributionPanel :data="this.data" />
          </b-col>
        </b-row>
    </div>
</template>

<script>
import AgeDistributionPanel from '../components/AgeDistributionPanel';
import GenderDistributionPanel from '../components/GenderDistributionPanel';

export default {
    name: "Statistics",
    components: {
      AgeDistributionPanel,
      GenderDistributionPanel,
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
