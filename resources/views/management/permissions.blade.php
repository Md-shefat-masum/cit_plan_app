<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role Permissions Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        [v-cloak] { display: none; }
    </style>
</head>
<body class="bg-gray-100">
    <div id="app" v-cloak>
        <!-- Token Section -->
        <div class="bg-white shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">Authentication Token</h2>
            <div class="flex gap-4">
                <textarea 
                    v-model="token" 
                    class="flex-1 border border-gray-300 rounded p-2" 
                    rows="3"
                    placeholder="JWT Token"
                ></textarea>
                <button 
                    @click="validateToken"
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    :disabled="!token || loading"
                >
                    Validate Token
                </button>
            </div>
            <div v-if="tokenStatus" class="mt-2">
                <span :class="tokenStatus === 'valid' ? 'text-green-600' : 'text-red-600'">
                    @{{ tokenStatus === 'valid' ? '✓ Token is valid (Super Admin)' : '✗ Invalid token or not Super Admin' }}
                </span>
            </div>
        </div>

        <!-- Filters + Endpoints: only after token validated -->
        <template v-if="isSuperAdmin">
            <!-- Sticky Filters -->
            <div class="bg-white shadow-md p-6 mb-6 sticky top-0 z-10">
                <h2 class="text-2xl font-bold mb-4">Filters</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Role</label>
                        <select 
                            v-model="selectedRole" 
                            class="w-full border border-gray-300 rounded p-2"
                            @change="onRoleChange"
                        >
                            <option value="">Select Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                @{{ role.title }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Module</label>
                        <select 
                            v-model="selectedModule" 
                            class="w-full border border-gray-300 rounded p-2"
                            @change="onModuleChange"
                        >
                            <option value="">All modules</option>
                            <option v-for="m in modules" :key="m.id" :value="m.id">
                                @{{ m.title }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Sub-module</label>
                        <select 
                            v-model="selectedSubModule" 
                            class="w-full border border-gray-300 rounded p-2"
                            :disabled="!selectedModule"
                        >
                            <option value="">All sub-modules</option>
                            <option v-for="sm in filteredSubModules" :key="sm.id" :value="sm.id">
                                @{{ sm.title }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap items-end gap-3">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium mb-2">Search by URI</label>
                        <input 
                            type="text"
                            v-model="searchUri"
                            placeholder="Type to filter endpoints by URI..."
                            class="w-full border border-gray-300 rounded p-2"
                        >
                    </div>
                    <button 
                        @click="clearFilters"
                        class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-100"
                    >
                        Clear
                    </button>
                </div>
            </div>

            <!-- Single section: view, filter, manage permissions (only when role selected) -->
            <div v-if="selectedRole" class="bg-white shadow-md p-6">
                <h2 class="text-2xl font-bold mb-4">
                    Permissions for @{{ getRoleName(selectedRole) }}
                </h2>
                <p class="text-gray-600 mb-4">
                    Check endpoints to grant permission; uncheck to revoke. Module / Sub-module filters only hide or show endpoints.
                </p>
                <div class="mb-4 flex flex-wrap gap-2">
                    <button 
                        @click="selectAllVisible"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                    >
                        Select all visible
                    </button>
                    <button 
                        @click="deselectAllVisible"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                    >
                        Deselect all visible
                    </button>
                    <button 
                        @click="showPreviewModal = true"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        :disabled="saving"
                    >
                        Save permissions
                    </button>
                    <button 
                        @click="endpointViewMode = 'all'"
                        :class="endpointViewMode === 'all' ? 'px-4 py-2 bg-gray-700 text-white rounded' : 'px-4 py-2 border border-gray-300 rounded hover:bg-gray-100'"
                    >
                        Show all endpoints
                    </button>
                    <button 
                        @click="endpointViewMode = 'permitted'"
                        :class="endpointViewMode === 'permitted' ? 'px-4 py-2 bg-gray-700 text-white rounded' : 'px-4 py-2 border border-gray-300 rounded hover:bg-gray-100'"
                    >
                        Show permitted endpoints
                    </button>
                </div>
                <div class="space-y-4 max-h-[70vh] overflow-y-auto">
                    <p v-if="endpointViewMode === 'permitted' && displayGroupedEndpoints.length === 0" class="text-gray-500 py-4">
                        No permitted endpoints for this role. Select endpoints above (with "Show all endpoints") and save.
                    </p>
                    <template v-for="module in displayGroupedEndpoints">
                        <div :key="module.id" class="border border-gray-200 rounded-lg overflow-hidden">
                            <h3 class="text-lg font-semibold bg-gray-100 px-4 py-2">
                                @{{ module.title }}
                            </h3>
                            <div class="divide-y divide-gray-100">
                                <template v-for="sub in module.sub_modules">
                                    <div :key="sub.id" class="px-4">
                                        <h4 class="text-sm font-medium text-gray-700 py-2">
                                            @{{ sub.title }}
                                        </h4>
                                        <div class="space-y-1 pb-3">
                                            <label 
                                                v-for="ep in sub.endpoints" 
                                                :key="ep.id"
                                                class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    :value="ep.id"
                                                    v-model="selectedEndpoints"
                                                    class="rounded"
                                                >
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate">
                                                        @{{ ep.title || ep.uri }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 truncate">
                                                        @{{ ep.uri }}
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- Preview modal: grouped permissions → Confirm & Save -->
        <div v-if="showPreviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] flex flex-col">
                <div class="p-4 border-b">
                    <h3 class="text-xl font-bold">Preview permissions</h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Role: <strong>@{{ getRoleName(selectedRole) }}</strong>
                        · @{{ selectedEndpoints.length }} endpoint(s) selected
                    </p>
                </div>
                <div class="p-4 overflow-y-auto flex-1 space-y-4">
                    <template v-for="m in previewGrouped">
                        <div :key="m.id" class="border border-gray-200 rounded-lg overflow-hidden">
                            <h4 class="text-base font-semibold bg-gray-100 px-3 py-2">@{{ m.title }}</h4>
                            <div class="divide-y divide-gray-100">
                                <template v-for="s in m.sub_modules">
                                    <div :key="s.id" class="px-3">
                                        <h5 class="text-sm font-medium text-gray-700 py-1.5">@{{ s.title }}</h5>
                                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-0.5 pb-2">
                                            <li v-for="ep in s.endpoints" :key="ep.id" class="truncate" :title="ep.uri">
                                                @{{ ep.title +' - '+ ep.uri }}
                                            </li>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <p v-if="previewGrouped.length === 0" class="text-gray-500 text-sm">
                        No permissions selected. Saving will revoke all for this role.
                    </p>
                </div>
                <div class="p-4 border-t flex gap-3 justify-end">
                    <button 
                        @click="showPreviewModal = false"
                        class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button 
                        @click="confirmAndSave"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                        :disabled="saving"
                    >
                        @{{ saving ? 'Saving...' : 'Confirm & Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                token: @json($token ?? ''),
                tokenStatus: '',
                isSuperAdmin: false,
                loading: false,
                saving: false,
                modules: [],
                roles: [],
                selectedRole: '',
                selectedModule: '',
                selectedSubModule: '',
                searchUri: '',
                selectedEndpoints: [],
                groupedEndpoints: [],
                endpointViewMode: 'all',
                showPreviewModal: false,
                apiBaseUrl: '/api/v1/permission-management'
            },
            computed: {
                previewGrouped() {
                    const sel = new Set(this.selectedEndpoints);
                    const out = [];
                    (this.groupedEndpoints || []).forEach(m => {
                        const subs = [];
                        (m.sub_modules || []).forEach(s => {
                            const eps = (s.endpoints || []).filter(ep => sel.has(ep.id));
                            if (eps.length) subs.push({ ...s, endpoints: eps });
                        });
                        if (subs.length) out.push({ ...m, sub_modules: subs });
                    });
                    return out;
                },
                filteredSubModules() {
                    if (!this.selectedModule) return [];
                    const m = this.groupedEndpoints.find(m => m.id == this.selectedModule);
                    return m ? (m.sub_modules || []) : [];
                },
                filteredGroupedEndpoints() {
                    let list = this.groupedEndpoints;
                    if (this.selectedModule) {
                        const mod = list.find(m => m.id == this.selectedModule);
                        if (!mod) return [];
                        let subs = mod.sub_modules || [];
                        if (this.selectedSubModule) {
                            subs = subs.filter(s => s.id == this.selectedSubModule);
                        }
                        list = [{ ...mod, sub_modules: subs }];
                    }
                    const q = (this.searchUri || '').trim().toLowerCase();
                    if (!q) return list;
                    const out = [];
                    list.forEach(m => {
                        const subs = [];
                        (m.sub_modules || []).forEach(s => {
                            const eps = (s.endpoints || []).filter(ep =>
                                (ep.uri || '').toLowerCase().includes(q)
                            );
                            if (eps.length) subs.push({ ...s, endpoints: eps });
                        });
                        if (subs.length) out.push({ ...m, sub_modules: subs });
                    });
                    return out;
                },
                displayGroupedEndpoints() {
                    const base = this.filteredGroupedEndpoints;
                    if (this.endpointViewMode !== 'permitted') return base;
                    const sel = new Set(this.selectedEndpoints);
                    const out = [];
                    base.forEach(m => {
                        const subs = [];
                        (m.sub_modules || []).forEach(s => {
                            const eps = (s.endpoints || []).filter(ep => sel.has(ep.id));
                            if (eps.length) subs.push({ ...s, endpoints: eps });
                        });
                        if (subs.length) out.push({ ...m, sub_modules: subs });
                    });
                    return out;
                },
                visibleEndpointIds() {
                    const ids = [];
                    this.displayGroupedEndpoints.forEach(m => {
                        (m.sub_modules || []).forEach(s => {
                            (s.endpoints || []).forEach(ep => ids.push(ep.id));
                        });
                    });
                    return ids;
                }
            },
            methods: {
                async validateToken() {
                    if (!this.token) {
                        Swal.fire('Error', 'Please enter a token', 'error');
                        return;
                    }
                    this.loading = true;
                    try {
                        const res = await axios.post(this.apiBaseUrl + '/validate-token', { token: this.token });
                        if (res.data.success && res.data.data.role_id === -1) {
                            this.tokenStatus = 'valid';
                            this.isSuperAdmin = true;
                            await this.loadData();
                        } else {
                            this.tokenStatus = 'invalid';
                            this.isSuperAdmin = false;
                            Swal.fire('Unauthorized', 'Token is not Super Admin', 'error');
                        }
                    } catch (e) {
                        this.tokenStatus = 'invalid';
                        this.isSuperAdmin = false;
                        Swal.fire('Error', e.response?.data?.message || 'Invalid token', 'error');
                    } finally {
                        this.loading = false;
                    }
                },
                async loadData() {
                    try {
                        const headers = { headers: { Authorization: 'Bearer ' + this.token } };
                        const [modRes, rolesRes, groupedRes] = await Promise.all([
                            axios.get(this.apiBaseUrl + '/modules', headers),
                            axios.get(this.apiBaseUrl + '/roles', headers),
                            axios.get(this.apiBaseUrl + '/endpoints-grouped', headers)
                        ]);
                        this.modules = modRes.data.data || [];
                        this.roles = rolesRes.data.data || [];
                        this.groupedEndpoints = groupedRes.data.data || [];
                    } catch (e) {
                        Swal.fire('Error', 'Failed to load data', 'error');
                    }
                },
                onRoleChange() {
                    this.selectedEndpoints = [];
                    if (this.selectedRole) this.loadRolePermissions();
                },
                async loadRolePermissions() {
                    try {
                        const res = await axios.get(
                            this.apiBaseUrl + '/role-permissions/' + this.selectedRole,
                            { headers: { Authorization: 'Bearer ' + this.token } }
                        );
                        const data = res.data.data || [];
                        this.selectedEndpoints = data
                            .map(p => p.app_module_sub_module_endpoint_id)
                            .filter(id => id);
                    } catch (e) {
                        console.error('Failed to load role permissions', e);
                        Swal.fire('Error', 'Failed to load role permissions', 'error');
                    }
                },
                onModuleChange() {
                    this.selectedSubModule = '';
                },
                clearFilters() {
                    this.selectedModule = '';
                    this.selectedSubModule = '';
                    this.searchUri = '';
                },
                getRoleName(roleId) {
                    const r = this.roles.find(x => x.id == roleId);
                    return r ? r.title : 'Unknown';
                },
                selectAllVisible() {
                    const add = this.visibleEndpointIds.filter(id => !this.selectedEndpoints.includes(id));
                    this.selectedEndpoints = [...this.selectedEndpoints, ...add];
                },
                deselectAllVisible() {
                    const hide = new Set(this.visibleEndpointIds);
                    this.selectedEndpoints = this.selectedEndpoints.filter(id => !hide.has(id));
                },
                async confirmAndSave() {
                    if (!this.selectedRole) return;
                    this.saving = true;
                    try {
                        const scopes = this.selectedEndpoints.map(id => ({
                            app_module_sub_module_endpoint_id: id
                        }));
                        await axios.post(
                            '/api/v1/add-permission-to-role',
                            { role_id: this.selectedRole, scopes },
                            { headers: { Authorization: 'Bearer ' + this.token } }
                        );
                        Swal.fire('Success', 'Permissions saved successfully', 'success');
                        this.showPreviewModal = false;
                    } catch (e) {
                        Swal.fire('Error', e.response?.data?.message || 'Failed to save permissions', 'error');
                    } finally {
                        this.saving = false;
                    }
                }
            }
        });
    </script>
</body>
</html>
