<template>
  <v-app>
    <div>
      <v-col
          cols="12"
          sm="6"
      >
        <v-text-field
            label="Username"
            @input="inputUsername"
        ></v-text-field>
      </v-col>

      <v-col
          cols="12"
          sm="6"
      >
        <v-text-field
            v-model="password"
            :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
            :rules="[rules.required, rules.min]"
            :type="show1 ? 'text' : 'password'"
            name="input-10-1"
            label="Password"
            hint="At least 4 characters"
            counter
            @click:append="show1 = !show1"
            @input="inputPassword"
        ></v-text-field>
      </v-col>

      <v-btn
          v-on:click="registerUser"
          text
          color="black"
      >
        Register
      </v-btn>
    </div>
  </v-app>
</template>

<script>
import axios from "axios";

let username;
let password;

export default {
  data() {
    return {
      show1: false,
      password: '',
      rules: {
        required: value => !!value || 'Required.',
        min: v => v.length >= 4 || 'Min 4 characters',
      },
    }
  },
  methods: {
    inputUsername: function (value) {
      username = value
    },
    inputPassword: function (value) {
      password = value
    },
    async registerUser() {
      await axios.put(
          "https://127.0.0.1:8000/api/register/", {params: {username: username,password: password}}
      )
    },
  }
}
</script>