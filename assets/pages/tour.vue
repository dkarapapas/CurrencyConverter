<template>
  <v-app>
    <div>
      <v-container fluid>
        <v-btn
            to="/"
            depressed
            color="primary"
        >
          Home
        </v-btn>

        <v-btn
            to="/currencyCRUD"
            depressed
            color="yellow"
        >
          Currencies
        </v-btn>

        <v-btn
            to="/currencyRateCRUD"
            depressed
            color="orange"
        >
          Currencies Rates
        </v-btn>

        <v-btn
            to="/register"
            depressed
            color="green"
        >
          Register
        </v-btn>
      </v-container>
    </div>

    <v-container fluid>
      <router-view></router-view>
    </v-container>
  </v-app>
</template>

<script>
export default {}
</script>