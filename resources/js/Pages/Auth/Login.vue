<script>
import Login from "@/Layouts/Login.vue";
import { onMounted } from "vue";
export default {
    layout: Login,
};
</script>

<script setup>
import { useConfiguracion } from "@/composables/configuracion/useConfiguracion";
import { useForm, Head } from "@inertiajs/vue3";
import { ref } from "vue";

const { oConfiguracion } = useConfiguracion();
const form = useForm({
    usuario: "",
    password: "",
});

const visible = ref(false);

const submit = () => {
    form.post(route("login"), {
        onError: (err) => {
            if (err.acceso) {
                Swal.fire({
                    icon: "info",
                    title: "Error",
                    text: `${err.acceso}`,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: `Aceptar`,
                });
            }
        },
        onFinish: () => form.reset("password"),
    });
};

const url_asset = ref("/");

onMounted(() => {
    url_asset.value = url_assets;
});
</script>

<template>
    <Head title="Login" />
    <v-container class="ma-0 login">
        <v-row align="center" justify="center">
            <v-col cols="12" md="6" xl="5">
                <v-card class="elevation-6 mt-10">
                    <v-row>
                        <v-col
                            cols="12"
                            xs="12"
                            sm="12"
                            md="6"
                            xl="6"
                            class="pa-0 hidden-sm-and-down"
                        >
                            <v-img
                                class="h-100 w-100"
                                lazy
                                cover
                                :src="url_asset + '/imgs/lateral.webp'"
                            ></v-img>
                        </v-col>
                        <v-col cols="12" sm="12" md="6" xl="6" class="pa-0">
                            <v-card class="pa-0" color="cyan-accent-4">
                                <v-card-text class="pb-0">
                                    <v-img
                                        :src="oConfiguracion.url_logo"
                                        class="w-50 mx-auto"
                                    ></v-img>
                                </v-card-text>
                                <v-card-title class="pt-0">
                                    <h4 class="text-center">
                                        {{ oConfiguracion.nombre }}
                                    </h4>
                                </v-card-title>
                                <form @submit.prevent="submit" class="">
                                    <v-card-text>
                                        <h4
                                            class="text-h5 text-center text-white"
                                        >
                                            Iniciar Sesión
                                        </h4>
                                        <p
                                            class="text-white text-caption text-center text-medium-emphasis"
                                        >
                                            Ingresa tu usuario y contraseña
                                        </p>
                                        <v-row
                                            align="center"
                                            justify="center"
                                            class="mb-8"
                                        >
                                            <v-col cols="12" sm="10">
                                                <div
                                                    class="text-white text-subtitle-1 text-medium-emphasis"
                                                >
                                                    Usuario
                                                </div>

                                                <v-text-field
                                                    density="compact"
                                                    :error="
                                                        form.errors?.usuario
                                                            ? true
                                                            : false
                                                    "
                                                    :error-messages="
                                                        form.errors?.usuario
                                                            ? form.errors
                                                                  ?.usuario
                                                            : ''
                                                    "
                                                    placeholder="Ingresa tu usuario"
                                                    prepend-inner-icon="mdi-account"
                                                    variant="outlined"
                                                    color="cyan-accent-1"
                                                    autocomplete="false"
                                                    v-model="form.usuario"
                                                    autofocus=""
                                                ></v-text-field>

                                                <div
                                                    class="text-white text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between"
                                                >
                                                    Contraseña
                                                </div>

                                                <v-text-field
                                                    :append-inner-icon="
                                                        visible
                                                            ? 'mdi-eye-off'
                                                            : 'mdi-eye'
                                                    "
                                                    :type="
                                                        visible
                                                            ? 'text'
                                                            : 'password'
                                                    "
                                                    :error="
                                                        form.errors?.password
                                                            ? true
                                                            : false
                                                    "
                                                    :error-messages="
                                                        form.errors?.password
                                                            ? form.errors
                                                                  ?.password
                                                            : ''
                                                    "
                                                    density="compact"
                                                    placeholder="Ingresa tu contraseña"
                                                    prepend-inner-icon="mdi-lock-outline"
                                                    variant="outlined"
                                                    color="cyan-accent-1"
                                                    @click:append-inner="
                                                        visible = !visible
                                                    "
                                                    autocomplete="false"
                                                    v-model="form.password"
                                                ></v-text-field>
                                                <v-btn
                                                    class="mt-2"
                                                    elevation="4"
                                                    rounded="0"
                                                    color="pink-accent-2"
                                                    dark
                                                    block
                                                    type="submit"
                                                    >Ingresar</v-btn
                                                >
                                            </v-col>
                                        </v-row>
                                    </v-card-text>
                                </form>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
.v-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    min-width: 100vw;
}
</style>
