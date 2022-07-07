<template>
  <v-app>
    <div>
      <v-container fluid>
        <v-row align="center">
          <v-col
              class="d-flex"
              cols="12"
              sm="6"
          >
            <v-select
                label="Select Currency"
                :items="currencies"
                item-text="description"
                item-value="id"
                @change="onChangeFrom(currencies.find(x=>x.id===$event).id)"
            ></v-select>
          </v-col>

          <v-col
              class="d-flex"
              cols="12"
              sm="6"
          >
            <v-select
                label="Select Currency"
                :items="currencies"
                item-text="description"
                item-value="id"
                @change="onChangeTo(currencies.find(x=>x.id===$event).id)"
            ></v-select>
          </v-col>

          <v-col
              cols="12"
              sm="6"
          >
            <v-text-field
                label="Amount"
                @input="input"
            ></v-text-field>
          </v-col>

          <v-col
              cols="12"
              sm="6"
          >
            <v-text-field
                v-model="rate"
                readonly
            ></v-text-field>
          </v-col>

          <v-btn
              v-on:click="fetchRates"
              text
              color="black"
          >
            Convert
          </v-btn>
        </v-row>
      </v-container>
    </div>
  </v-app>
</template>

<script>
import axios from "axios";

let currencyFrom;
let currencyTo;
let amount;
let convertedRate;

export default {
  data() {
    return {
      currencies: [],
      rate: '',
    };
  },
  mounted() {
    this.fetchCurrencies();
  },
  methods: {
    async fetchCurrencies() {
      let response = await axios.get("https://127.0.0.1:8000/api/currencies");
      this.currencies = response.data;
    },
    onChangeFrom(event) {
      currencyFrom = event;
    },
    onChangeTo(event) {
      currencyTo = event;
    },
    async fetchRates() {
      if (currencyFrom === currencyTo) {
        this.rate = amount;
      } else {
        let rate = await axios.get(
            "https://127.0.0.1:8000/api/currencies/getRates/" + currencyFrom + "/" + currencyTo
        );
        convertedRate = amount * rate.data;
        this.rate = convertedRate;
      }
    },
    input: function (value) {
      amount = value;
    },
  }
};
</script>