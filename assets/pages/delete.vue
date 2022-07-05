<template>
  <v-data-table
      :headers="headers"
      :items="currencies"
      class="elevation-1"
  >
    <template v-slot:top>
      <v-toolbar
          flat
      >
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
          @click="openDialog(item)"
      >
        mdi-delete
      </v-icon>
    </template>
  </v-data-table>
</template>

<script>
import axios from "axios";
let selectedCurrencyID;

export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    headers: [
      {
        text: 'Currency',
        align: 'start',
        sortable: false,
        value: 'description'
      },
      {
        text: 'Actions',
        value: 'actions',
        align: 'end',
        sortable: false
      },
    ],
    currencies: [],
    editedID: 0,
    editedItem: {
      id: '',
    },
    defaultItem: {
      id: ''
    },
  }),

  watch: {
    dialogDelete(val) {
      val || this.closeDelete()
    },
  },

  created() {
    this.initialize()
  },

  methods: {
    async initialize() {
      let response = await axios.get(
          "https://127.0.0.1:8000/api/currencies"
      );
      this.currencies = response.data;
    },

    openDialog(item){
      selectedCurrencyID = item.id
      this.dialogDelete = true
    },

    async deleteItemConfirm() {
      let selectedIndex = this.currencies.map(item=>item.id).indexOf(selectedCurrencyID)
      this.currencies.splice(selectedIndex, 1)
      this.closeDelete()
      await axios.delete(
          "https://127.0.0.1:8000/api/currencies/" + selectedCurrencyID
      )
    },

    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
      })
    },
  },
}
</script>