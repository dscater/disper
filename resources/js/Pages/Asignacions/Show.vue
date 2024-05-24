<script>
const breadbrums = [
    {
        title: "Inicio",
        disabled: false,
        url: route("inicio"),
        name_url: "inicio",
    },
    {
        title: "Asignación",
        disabled: false,
        url: route("asignacions.index"),
        name_url: "asignacions.index",
    },
    {
        title: "Nuevo",
        disabled: false,
        url: "",
        name_url: "",
    },
];
</script>
<script setup>
import BreadBrums from "@/Components/BreadBrums.vue";
import { useApp } from "@/composables/useApp";
import { Head, router } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { useMenu } from "@/composables/useMenu";
import { useAsignacions } from "@/composables/asignacions/useAsignacions";
const { mobile, identificaDispositivo } = useMenu();
const { setLoading } = useApp();
const { oAsignacion, limpiarAsignacion } = useAsignacions();
limpiarAsignacion();

const props = defineProps({
    asignacion: {
        type: Object,
    },
});

const cargaMapaGoogle = async () => {
    console.log(mapa_id);
    console.log(props.asignacion.asignacion_detalles[0].lat);
    console.log(props.asignacion.asignacion_detalles[0].lng);

    // Inicializa el mapa
    const { Map, InfoWindow } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");
    const map = new Map(document.getElementById("google_map"), {
        zoom: 13,
        center: {
            lat: Number(props.asignacion.asignacion_detalles[0].lat),
            lng: Number(props.asignacion.asignacion_detalles[0].lng),
        },
        mapId: mapa_id,
    });
    const infoWindow = new InfoWindow();

    // Crear los marcadores
    props.asignacion.asignacion_detalles.forEach(function (markerData) {
        console.log(markerData.total_personal)
        var AME = new AdvancedMarkerElement({
            map,
            position: {
                lat: Number(markerData.lat),
                lng: Number(markerData.lng),
            },
            gmpDraggable: false,
            title: "ASDAS",
        });
    });
};

function toggleHighlight(markerView, property) {
    console.log(markerView.content);
    console.log(markerView.zIndex);
    if (markerView.content.classList.contains("highlight")) {
        markerView.content.classList.remove("highlight");
        markerView.zIndex = 1;
    } else {
        markerView.content.classList.add("highlight");
        markerView.zIndex = 1;
    }
}

const volver = () => {
    router.get(route("asignacions.index"));
};

onMounted(() => {
    setTimeout(() => {
        setLoading(false);
        cargaMapaGoogle();
    }, 300);
});
</script>
<template>
    <Head title="Asignacions"></Head>
    <v-container>
        <BreadBrums :breadbrums="breadbrums"></BreadBrums>
        <v-row class="mt-0">
            <v-col cols="12" class="d-flex justify-end">
                <v-btn prepend-icon="mdi-arrow-left" @click="volver"
                    >Volver</v-btn
                >
            </v-col>
        </v-row>
        <v-row class="mt-0">
            <v-col cols="12">
                <v-card>
                    <v-card-title class="bg-cyan-accent-4 pa-5">
                        <span class="text-h5"
                            >Asignación: {{ props.asignacion.cod }}</span
                        >
                        <p class="text-body2">
                            <strong>Fecha: </strong>
                            {{ props.asignacion.fecha_registro_t }}
                        </p>
                    </v-card-title>
                    <v-card-text>
                        <v-row class="py-3">
                            <v-col cols="12">
                                <!-- <span class="text-body-1"
                                    >Mueva el marcador
                                    <i class="mdi mdi-map-marker text-red"></i>
                                    a la ubicación deseada</span
                                > -->
                            </v-col>
                        </v-row>
                        <div id="google_map"></div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
<style>
#google_map {
    height: 400px;
}
</style>
