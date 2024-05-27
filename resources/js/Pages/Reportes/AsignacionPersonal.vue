<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Asignaciones de Personal",
        disabled: false,
        url: "",
        name_url: "",
    },
];
</script>

<script setup>
import BreadBrums from "@/Components/BreadBrums.vue";
import { useApp } from "@/composables/useApp";
import { computed, onMounted, ref } from "vue";
import { Head, usePage } from "@inertiajs/vue3";
import { useAsignacions } from "@/composables/asignacions/useAsignacions";
import { usePersonals } from "@/composables/personals/usePersonals";
import axios from "axios";
const { setLoading } = useApp();

onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { getAsignacions } = useAsignacions();
const { getPersonals } = usePersonals();
const form = ref({
    asignacion_id: "todos",
    personal_id: "todos",
    lugar: "TODOS",
    fecha_ini: "",
    fecha_fin: "",
});

const generando = ref(false);
const txtBtn = computed(() => {
    if (generando.value) {
        return "Generando Reporte...";
    }
    return "Generar Reporte";
});

const listAsignacions = ref([]);
const listPersonals = ref([]);
const listLugars = ref([]);

const generarReporte = () => {
    generando.value = true;
    const url = route("reportes.r_asignacion_personal", form.value);
    window.open(url, "_blank");
    setTimeout(() => {
        generando.value = false;
    }, 500);
};

const getLugares = () => {
    axios.get(route("lugars.getLugarUnico")).then((response) => {
        listLugars.value = response.data;
        listLugars.value.unshift("TODOS");
    });
};

const cargarListas = async () => {
    listAsignacions.value = await getAsignacions();
    listAsignacions.value.unshift({
        id: "todos",
        cod: "TODOS",
    });
    listPersonals.value = await getPersonals();
    listPersonals.value.unshift({
        id: "todos",
        full_name: "TODOS",
    });

    getLugares();
};

onMounted(() => {
    cargarListas();
});
</script>
<template>
    <Head title="Reporte Asignaciones de Personal"></Head>
    <v-container>
        <BreadBrums :breadbrums="breadbrums"></BreadBrums>
        <v-row>
            <v-col cols="12" sm="12" md="12" xl="8" class="mx-auto">
                <v-card>
                    <v-card-item>
                        <v-container>
                            <form @submit.prevent="generarReporte">
                                <v-row>
                                    <v-col cols="12" md="6">
                                        <v-autocomplete
                                            :hide-details="
                                                form.errors?.asignacion_id
                                                    ? false
                                                    : true
                                            "
                                            :error="
                                                form.errors?.asignacion_id
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                form.errors?.asignacion_id
                                                    ? form.errors?.asignacion_id
                                                    : ''
                                            "
                                            variant="outlined"
                                            density="compact"
                                            required
                                            :items="listAsignacions"
                                            item-value="id"
                                            item-title="cod"
                                            label="Código de Asignación*"
                                            v-model="form.asignacion_id"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-autocomplete
                                            :hide-details="
                                                form.errors?.personal_id
                                                    ? false
                                                    : true
                                            "
                                            :error="
                                                form.errors?.personal_id
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                form.errors?.personal_id
                                                    ? form.errors?.personal_id
                                                    : ''
                                            "
                                            variant="outlined"
                                            density="compact"
                                            required
                                            :items="listPersonals"
                                            item-value="id"
                                            item-title="full_name"
                                            label="Personal*"
                                            v-model="form.personal_id"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-autocomplete
                                            :hide-details="
                                                form.errors?.lugar
                                                    ? false
                                                    : true
                                            "
                                            :error="
                                                form.errors?.lugar
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                form.errors?.lugar
                                                    ? form.errors?.lugar
                                                    : ''
                                            "
                                            variant="outlined"
                                            density="compact"
                                            required
                                            :items="listLugars"
                                            item-value="id"
                                            item-title="full_name"
                                            label="Zona*"
                                            v-model="form.lugar"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            :hide-details="
                                                form.errors?.fecha_ini
                                                    ? false
                                                    : true
                                            "
                                            :error="
                                                form.errors?.fecha_ini
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                form.errors?.fecha_ini
                                                    ? form.errors?.fecha_ini
                                                    : ''
                                            "
                                            variant="outlined"
                                            type="date"
                                            label="Fecha Inicial"
                                            density="compact"
                                            v-model="form.fecha_ini"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-text-field
                                            :hide-details="
                                                form.errors?.fecha_fin
                                                    ? false
                                                    : true
                                            "
                                            :error="
                                                form.errors?.fecha_fin
                                                    ? true
                                                    : false
                                            "
                                            :error-messages="
                                                form.errors?.fecha_fin
                                                    ? form.errors?.fecha_fin
                                                    : ''
                                            "
                                            variant="outlined"
                                            type="date"
                                            label="Fecha Fin"
                                            density="compact"
                                            v-model="form.fecha_fin"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-btn
                                            color="cyan-darken-2"
                                            block
                                            @click="generarReporte"
                                            :disabled="generando"
                                            v-text="txtBtn"
                                        ></v-btn>
                                    </v-col>
                                </v-row>
                            </form>
                        </v-container>
                    </v-card-item>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
