<template>
    <v-container grid-list-md fuid>
        <v-layout>
            <v-flex xs12>
                <h4 class="display-1 font-weight-regular">Welcome back, {{ userFirstName(user.name) }}</h4>
                <Invoices v-if="isUserManager"></Invoices>
                <Meals v-if="isUserManager"></Meals>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import Invoices from "./cashier/Invoices";
    import Meals from "./waiter/Meals"
    import {toasts, helper} from "../../mixin"

    export default {
        name: "Dashboard",
        mixins: [toasts, helper],
        components: {Invoices, Meals},
        computed: {
            user() {
                return this.$store.state.user;
            }
        }
    }
</script>

<style scoped>

</style>