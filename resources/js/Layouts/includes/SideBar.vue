<script setup>
import { useMenu } from "@/composables/useMenu";
import { onMounted, ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { useUser } from "@/composables/useUser";
const { oUser } = useUser();

// data
const {
    mobile,
    drawer,
    rail,
    width,
    menu_open,
    setMenuOpen,
    cambiarUrl,
    toggleDrawer,
} = useMenu();

const user_logeado = ref({
    permisos: [],
});

const submenus = {
    "reportes.usuarios": "Reportes",
};

const route_current = ref("");

router.on("navigate", (event) => {
    route_current.value = route().current();
    if (mobile.value) {
        toggleDrawer(false);
    }
});

const { props: props_page } = usePage();

onMounted(() => {
    let route_actual = route().current();
    // buscar en submenus y abrirlo si uno de sus elementos esta activo
    setMenuOpen([]);
    if (submenus[route_actual]) {
        setMenuOpen([submenus[route_actual]]);
    }

    if (props_page.auth) {
        user_logeado.value = props_page.auth?.user;
    }

    setTimeout(() => {
        scrollActive();
    }, 300);
});

const scrollActive = () => {
    let sidebar = document.querySelector("#sidebar");
    let menu = null;
    let activeChild = null;
    if (sidebar) {
        menu = sidebar.querySelector(".v-navigation-drawer__content");
        activeChild = sidebar.querySelector(".active");
    }
    // Verifica si se encontró un hijo activo
    if (activeChild) {
        let offsetTop = activeChild.offsetTop - sidebar.offsetTop;
        setTimeout(() => {
            menu.scrollTo({
                top: offsetTop,
                behavior: "smooth", // desplazamiento suave
            });
        }, 500);
    }
};
</script>
<template>
    <v-navigation-drawer
        :permanent="!mobile"
        :temporary="mobile"
        v-model="drawer"
        class="border-0 elevation-2 __sidebar bg-grey-darken-4"
        :width="width"
        id="sidebar"
    >
        <v-sheet>
            <div
                class="w-100 d-flex flex-column align-center elevation-1 bg-cyan-accent-4 pa-2 __info_usuario"
            >
                <v-avatar
                    class="mb-1"
                    color="cyan-accent-3"
                    :size="`${!rail ? '64' : '32'}`"
                >
                    <v-img
                        cover
                        v-if="oUser.url_foto"
                        :src="oUser.url_foto"
                    ></v-img>
                    <span v-else class="text-h5">
                        {{ oUser.iniciales_nombre }}
                    </span>
                </v-avatar>
                <div v-show="!rail" class="text-caption font-weight-bold">
                    {{ oUser.tipo }}
                </div>
                <div v-show="!rail" class="text-body">
                    {{ oUser.full_name }}
                </div>
            </div>
        </v-sheet>

        <v-list class="mt-1 px-2" v-model:opened="menu_open">
            <v-list-item class="text-caption">
                <span v-if="rail && !mobile" class="text-center d-block"
                    ><v-icon>mdi-dots-horizontal</v-icon></span
                >
                <span v-else>INICIO</span></v-list-item
            >
            <v-list-item
                prepend-icon="mdi-home-outline"
                :class="[route_current == 'inicio' ? 'active' : '']"
                @click="cambiarUrl(route('inicio'))"
                link
            >
                <v-list-item-title>Inicio</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Inicio</v-tooltip
                >
            </v-list-item>
            <v-list-item
                class="text-caption"
                v-if="
                    oUser.permisos.includes('usuarios.index') ||
                    oUser.permisos.includes('personals.index') ||
                    oUser.permisos.includes('entrenamientos.index')||
                    oUser.permisos.includes('asignacions.index')
                "
            >
                <span v-if="rail && !mobile" class="text-center d-block"
                    ><v-icon>mdi-dots-horizontal</v-icon></span
                >
                <span v-else>ADMINISTRACIÓN</span></v-list-item
            >
            <v-list-item
                :class="[route_current == 'personals.index' ? 'active' : '']"
                v-if="oUser.permisos.includes('personals.index')"
                prepend-icon="mdi-account-multiple"
                @click="cambiarUrl(route('personals.index'))"
                link
            >
                <v-list-item-title>Personal</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Personal</v-tooltip
                >
            </v-list-item>
            <v-list-item
                :class="[
                    route_current == 'entrenamientos.index' ? 'active' : '',
                ]"
                v-if="oUser.permisos.includes('entrenamientos.index')"
                prepend-icon="mdi-image-multiple"
                @click="cambiarUrl(route('entrenamientos.index'))"
                link
            >
                <v-list-item-title>Entrenamiento</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Entrenamiento</v-tooltip
                >
            </v-list-item>
            <v-list-item
                :class="[
                    route_current == 'asignacions.index' ? 'active' : '',
                ]"
                v-if="oUser.permisos.includes('asignacions.index')"
                prepend-icon="mdi-image-multiple"
                @click="cambiarUrl(route('asignacions.index'))"
                link
            >
                <v-list-item-title>Asignación de Personal</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Asignación de Personal</v-tooltip
                >
            </v-list-item>

            <v-list-item
                :class="[route_current == 'usuarios.index' ? 'active' : '']"
                v-if="oUser.permisos.includes('usuarios.index')"
                prepend-icon="mdi-account-group"
                @click="cambiarUrl(route('usuarios.index'))"
                link
            >
                <v-list-item-title>Usuarios</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Usuarios</v-tooltip
                >
            </v-list-item>

            <v-list-item
                class="text-caption"
                v-if="
                    oUser.permisos.includes('reportes.usuarios') ||
                    oUser.permisos.includes('reportes.historial_pacientes') ||
                    oUser.permisos.includes('reportes.pacientes') ||
                    oUser.permisos.includes('reportes.diagnosticos')
                "
                ><span v-if="rail && !mobile" class="text-center d-block"
                    ><v-icon>mdi-dots-horizontal</v-icon></span
                >
                <span v-else>REPORTES</span></v-list-item
            >
            <!-- SUBGROUP -->
            <v-list-group
                value="Reportes"
                v-if="
                    oUser.permisos.includes('reportes.usuarios') ||
                    oUser.permisos.includes('reportes.historial_pacientes') ||
                    oUser.permisos.includes('reportes.pacientes') ||
                    oUser.permisos.includes('reportes.diagnosticos')
                "
            >
                <template v-slot:activator="{ props }">
                    <v-list-item
                        v-bind="props"
                        prepend-icon="mdi-file-document-multiple"
                        title="Reportes"
                        :class="[
                            route_current == 'reportes.usuarios' ||
                            route_current == 'reportes.historial_pacientes' ||
                            route_current == 'reportes.pacientes' ||
                            route_current == 'reportes.diagnosticos'
                                ? 'active'
                                : '',
                        ]"
                    >
                        <v-tooltip
                            v-if="rail && !mobile"
                            color="white"
                            activator="parent"
                            location="end"
                            >Reportes</v-tooltip
                        ></v-list-item
                    >
                </template>
                <v-list-item
                    v-if="oUser.permisos.includes('reportes.usuarios')"
                    prepend-icon="mdi-chevron-right"
                    title="Usuarios"
                    :class="[
                        route_current == 'reportes.usuarios' ? 'active' : '',
                    ]"
                    @click="cambiarUrl(route('reportes.usuarios'))"
                    link
                >
                    <v-tooltip
                        v-if="rail && !mobile"
                        color="white"
                        activator="parent"
                        location="end"
                        >Usuarios</v-tooltip
                    ></v-list-item
                >
                <v-list-item
                    v-if="
                        oUser.permisos.includes('reportes.historial_pacientes')
                    "
                    prepend-icon="mdi-chevron-right"
                    title="Historial de Pacientes"
                    :class="[
                        route_current == 'reportes.historial_pacientes'
                            ? 'active'
                            : '',
                    ]"
                    @click="cambiarUrl(route('reportes.historial_pacientes'))"
                    link
                >
                    <v-tooltip
                        v-if="rail && !mobile"
                        color="white"
                        activator="parent"
                        location="end"
                        >Historial de Pacientes</v-tooltip
                    ></v-list-item
                >
                <v-list-item
                    v-if="oUser.permisos.includes('reportes.pacientes')"
                    prepend-icon="mdi-chevron-right"
                    title="Lista de Pacientes"
                    :class="[
                        route_current == 'reportes.pacientes' ? 'active' : '',
                    ]"
                    @click="cambiarUrl(route('reportes.pacientes'))"
                    link
                >
                    <v-tooltip
                        v-if="rail && !mobile"
                        color="white"
                        activator="parent"
                        location="end"
                        >Lista de Pacientes</v-tooltip
                    ></v-list-item
                >
                <v-list-item
                    v-if="oUser.permisos.includes('reportes.diagnosticos')"
                    prepend-icon="mdi-chevron-right"
                    title="Diagnósticos por Imagen"
                    :class="[
                        route_current == 'reportes.diagnosticos'
                            ? 'active'
                            : '',
                    ]"
                    @click="cambiarUrl(route('reportes.diagnosticos'))"
                    link
                >
                    <v-tooltip
                        v-if="rail && !mobile"
                        color="white"
                        activator="parent"
                        location="end"
                        >Diagnósticos por Imagen</v-tooltip
                    ></v-list-item
                >
            </v-list-group>
            <v-list-item class="text-caption"
                ><span v-if="rail && !mobile" class="text-center d-block"
                    ><v-icon>mdi-dots-horizontal</v-icon></span
                >
                <span v-else>OTROS</span></v-list-item
            >
            <v-list-item
                v-if="oUser.permisos.includes('configuracions.index')"
                prepend-icon="mdi-cog-outline"
                :class="[
                    route_current == 'configuracions.index' ? 'active' : '',
                ]"
                @click="cambiarUrl(route('configuracions.index'))"
                link
            >
                <v-list-item-title>Configuración</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Configuración</v-tooltip
                >
            </v-list-item>
            <v-list-item
                prepend-icon="mdi-account-circle"
                :class="[route_current == 'profile.edit' ? 'active' : '']"
                @click="cambiarUrl(route('profile.edit'))"
                link
            >
                <v-list-item-title>Perfil</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Perfil</v-tooltip
                >
            </v-list-item>
            <v-list-item
                prepend-icon="mdi-logout"
                @click="cambiarUrl(route('logout'), 'post')"
                link
            >
                <v-list-item-title>Salir</v-list-item-title>
                <v-tooltip
                    v-if="rail && !mobile"
                    color="white"
                    activator="parent"
                    location="end"
                    >Salir</v-tooltip
                >
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>
<style scoped></style>
