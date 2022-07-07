<template>
  <v-data-table
      :headers="headers"
      :items="currencyRates"
      class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar
          flat
      >
        <v-spacer></v-spacer>
        <v-dialog
            v-model="dialog"
            max-width="500px"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="primary"
                dark
                class="mb-2"
                v-bind="attrs"
                v-on="on"
            >
              Add
            </v-btn>
          </template>
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-form class="px-3" ref="form">
                <v-container>
                  <v-row>
                    <v-col
                        class="d-flex"
                        cols="12"
                        sm="6"
                    >
                      <v-select
                          v-model="editedItem.currencyFromID"
                          label="Select Currency From"
                          :items="currencies"
                          item-text="description"
                          item-value="id"
                          :rules="[rulesCurrencyFromID.required]"
                          :disabled = "isEdit == 1"
                          @change="onChangeFrom(currencies.find(x=>x.id===$event).id)"
                      ></v-select>
                    </v-col>

                    <v-col
                        class="d-flex"
                        cols="12"
                        sm="6"
                    >
                      <v-select
                          v-model="editedItem.currencyToID"
                          label="Select Currency To"
                          :items="currencies"
                          item-text="description"
                          item-value="id"
                          :rules="[rulesCurrencyToID.required]"
                          :disabled = "isEdit == 1"
                          @change="onChangeTo(currencies.find(x=>x.id===$event).id)"
                      ></v-select>
                    </v-col>

                    <v-col
                        cols="12"
                        sm="6"
                        md="4"
                    >
                      <v-text-field
                          v-model="editedItem.rate"
                          :rules="[rulesRates.counter]"
                          counter
                          maxlength="10"
                          label="Rate"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-form>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                  color="blue darken-1"
                  text
                  @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this item?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>
    <template v-slot:item.actions="{ item }">
      <v-icon
          small
          class="mr-2"
          @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>

      <v-icon
          small
          @click="openDialog(item)"
      >
        mdi-delete
      </v-icon>
    </template>
  </v-data-table>
</template>

<script>
import axios from "axios";

let selectedIDFrom;
let selectedIDTo;
let currencyFromDropdown;
let currencyToDropdown;

export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    rulesCurrencyFromID: {
      required: (value) => !!value || 'Required.',
    },
    rulesCurrencyToID: {
      required: (value) => !!value || 'Required.',
    },
    rulesRates: {
      counter: value => value.length <= 10 || 'Max 10 characters',
    },
    headers: [
      {
        text: 'Currency From',
        align: 'start',
        sortable: false,
        value: 'currencyFromDesc'
      },
      {
        text: 'Currency To',
        align: 'start',
        sortable: false,
        value: 'currencyToDesc'
      },
      {
        text: 'Rate',
        align: 'start',
        sortable: false,
        value: 'rate'
      },
      {
        text: 'Actions',
        value: 'actions',
        align: 'end',
        sortable: false
      },
    ],
    currencies: [],
    currencyRates: [],
    editedIndex: -1,
    isEdit: 0,
    editedItem: {
      currencyFromID: '',
      currencyFromDesc: '',
      currencyToID: '',
      currencyToDesc: '',
      rate: ''
    },
    defaultItem: {
      currencyFromID: '',
      currencyFromDesc: '',
      currencyToID: '',
      currencyToDesc: '',
      rate: ''
    },
  }),
  watch: {
    dialog(val) {
      val || this.close()
    },
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'New Currency Rate' : 'Edit Currency Rate'
    },
  },
  created() {
    this.initialize()
  },
  methods: {
    async initialize() {
      let responseCurrencies = await axios.get("https://127.0.0.1:8000/api/currencies");
      let responseCurrenciesRates = await axios.get("https://127.0.0.1:8000/api/currenciesRates")
      this.currencies = responseCurrencies.data;
      this.currencyRates = responseCurrenciesRates.data;
    },
    onChangeFrom(event) {
      currencyFromDropdown = event;
    },
    onChangeTo(event) {
      currencyToDropdown = event;
    },
    openDialog(item) {
      selectedIDFrom = item.currencyFromID
      selectedIDTo = item.currencyToID
      this.dialogDelete = true
    },
    async deleteItemConfirm() {
      let selectedIndex = this.currencyRates.map(item => item.currencyFromID | item.currencyToID)
                                             .indexOf(selectedIDFrom,selectedIDFrom)
      this.currencyRates.splice(selectedIndex, 1)
      this.closeDelete()
      await axios.delete("https://127.0.0.1:8000/api/currenciesRates/" + selectedIDFrom + "/" + selectedIDTo)
    },
    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
      })
    },
    close() {
      this.dialog = false
      this.isEdit = 0
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    editItem(item) {
      this.editedIndex = this.currencyRates.indexOf(item)
      this.isEdit = 1
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    async save() {
      if (this.$refs.form.validate()) {
        if (this.editedIndex > -1) {
          await axios.put("https://127.0.0.1:8000/api/currenciesRates/", {
            currencyFromID: this.editedItem.currencyFromID,
            currencyToID: this.editedItem.currencyToID,
            rates: this.editedItem.rate
          })
          Object.assign(this.currencyRates[this.editedIndex], this.editedItem)
        } else {
          await axios.post("https://127.0.0.1:8000/api/currenciesRates/", {
            currencyFromID: currencyFromDropdown,
            currencyToID: currencyToDropdown,
            rates: this.editedItem.rate
          }).then(response => {
            this.editedItem.rates = response.data.rate
            this.editedItem.currencyToID = response.data.currencyToID
            this.editedItem.currencyFromDesc= response.data.currencyFromDesc
            this.editedItem.currencyFromID= response.data.currencyFromID
            this.editedItem.currencyToDesc= response.data.currencyToDesc
          })
          this.currencyRates.push(this.editedItem);
        }
        this.close()
      }
    },
  },
}
</script>