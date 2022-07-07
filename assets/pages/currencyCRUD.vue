<template>
  <v-data-table
      :headers="headers"
      :items="currencies"
      sort-by="description"
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
                        cols="12"
                        sm="6"
                        md="4"
                    >
                      <v-text-field
                          v-model="editedItem.description"
                          :rules="[rulesDescription.counter]"
                          counter
                          maxlength="30"
                          label="Description"
                      ></v-text-field>
                    </v-col>

                    <v-col
                        cols="12"
                        sm="6"
                        md="4"
                    >
                      <v-text-field
                          v-model="editedItem.identifier"
                          :rules="[rulesIdentifier.required, rulesIdentifier.counter]"
                          maxlength="3"
                          label="Code"
                      ></v-text-field>
                    </v-col>
                    <v-col
                        cols="12"
                        sm="6"
                        md="4"
                    >
                      <v-text-field
                          v-model="editedItem.icon"
                          :rules="[rulesIcon.counter]"
                          counter
                          maxlength="10"
                          label="Symbol"
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

let selectedCurrencyID;

export default {
  data: () => ({
    dialog: false,
    dialogDelete: false,
    rulesDescription: {
      counter: value => value.length <= 30 || 'Max 30 characters',
    },
    rulesIdentifier: {
      required: (value) => !!value || 'Required.',
      counter: value => value.length <= 3 || 'Max 3 characters'
    },
    rulesIcon: {
      counter: value => value.length <= 10 || 'Max 10 characters',
    },
    headers: [
      {
        text: 'Description',
        align: 'start',
        sortable: false,
        value: 'description'
      },
      {
        text: 'Code',
        align: 'start',
        sortable: false,
        value: 'identifier'
      },
      {
        text: 'Symbol',
        align: 'start',
        sortable: false,
        value: 'icon'
      },
      {
        text: 'Actions',
        value: 'actions',
        align: 'end',
        sortable: false
      },
    ],
    currencies: [],
    editedIndex: -1,
    editedItem: {
      id: '',
      identifier: '',
      description: '',
      icon: ''
    },
    defaultItem: {
      id: '',
      identifier: '',
      description: '',
      icon: ''
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
      return this.editedIndex === -1 ? 'New Currency' : 'Edit Currency'
    },
  },
  created() {
    this.initialize()
  },
  methods: {
    async initialize() {
      let response = await axios.get("https://127.0.0.1:8000/api/currencies")
      this.currencies = response.data;
    },
    openDialog(item) {
      selectedCurrencyID = item.id
      this.dialogDelete = true
    },
    async deleteItemConfirm() {
      let selectedIndex = this.currencies.map(item => item.id).indexOf(selectedCurrencyID)
      this.currencies.splice(selectedIndex, 1)
      this.closeDelete()
      await axios.delete("https://127.0.0.1:8000/api/currencies/" + selectedCurrencyID)
    },
    closeDelete() {
      this.dialogDelete = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
      })
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      })
    },
    editItem(item) {
      this.editedIndex = this.currencies.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true
    },
    async save() {
      if (this.$refs.form.validate()) {
        if (this.editedIndex > -1) {
          await axios.put("https://127.0.0.1:8000/api/currencies/", {
            id: this.editedItem.id,
            currencyIdentifier: this.editedItem.identifier,
            description: this.editedItem.description,
            icon: this.editedItem.icon
          })
          Object.assign(this.currencies[this.editedIndex], this.editedItem)
        } else {
          await axios.post("https://127.0.0.1:8000/api/currencies/", {
            currencyIdentifier: this.editedItem.identifier,
            description: this.editedItem.description,
            icon: this.editedItem.icon
          }).then(response => this.editedItem.id = response.data)
          this.currencies.push(this.editedItem);
        }
        this.close()
      }
    },
  },
}
</script>