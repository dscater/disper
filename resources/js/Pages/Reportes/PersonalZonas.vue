<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Reporte Personal en Zonas",
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
import Highcharts from "highcharts";
import exporting from "highcharts/modules/exporting";
import axios from "axios";

exporting(Highcharts);
Highcharts.setOptions({
    lang: {
        downloadPNG: "Descargar PNG",
        downloadJPEG: "Descargar JPEG",
        downloadPDF: "Descargar PDF",
        downloadSVG: "Descargar SVG",
        printChart: "Imprimir gráfico",
        contextButtonTitle: "Menú de exportación",
        viewFullscreen: "Pantalla completa",
        exitFullscreen: "Salir de pantalla completa",
    },
});

const { setLoading } = useApp();

onMounted(() => {
    setTimeout(() => {
        setLoading(false);
    }, 300);
});

const { getAsignacions } = useAsignacions();
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
const generarReporte = async () => {
    generando.value = true;
    axios
        .get(route("reportes.r_personal_zonas"), {
            params: form.value,
        })
        .then((response) => {
            console.log(response.data.categories);
            console.log(response.data.data);

            // Create the chart
            Highcharts.chart("container", {
                chart: {
                    type: "column",
                },
                title: {
                    align: "center",
                    text: "Personal en zonas",
                },
                subtitle: {
                    align: "left",
                    text: "",
                },
                accessibility: {
                    announceNewData: {
                        enabled: true,
                    },
                },
                xAxis: {
                    type: "category",
                },
                yAxis: {
                    title: {
                        text: "Total",
                    },
                },
                legend: {
                    enabled: true,
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: "{point.y:.0f}",
                        },
                    },
                },

                tooltip: {
                    headerFormat:
                        '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat:
                        '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b><br/>',
                },
                series: [
                    {
                        name: "Total",
                        colorByPoint: true,
                        data: response.data.data,
                    },
                ],
            });
            generando.value = false;
        });
};

const cargarListas = async () => {
    listAsignacions.value = await getAsignacions();
    listAsignacions.value.unshift({
        id: "todos",
        cod: "TODOS",
    });
};

onMounted(() => {
    cargarListas();
});
</script>
<template>
    <Head title="Reporte Personal en Zonas"></Head>
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
            <v-col cols="12">
                <div id="container"></div>
            </v-col>
        </v-row>
    </v-container>
</template>
