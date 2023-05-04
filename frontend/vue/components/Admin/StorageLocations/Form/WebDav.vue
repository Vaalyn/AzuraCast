<template>
    <section class="card mt-3">
        <div class="card-header bg-primary-dark">
            <h2 class="card-title">
                {{ $gettext('Remote: WebDAV') }}
            </h2>
        </div>
        <b-card-body>
            <b-form-group>
                <div class="form-row">
                    <b-wrapped-form-group
                        id="form_edit_webDavBaseUri"
                        class="col-md-12 col-lg-6"
                        :field="form.webDavBaseUri"
                    >
                        <template #label>
                            {{ $gettext('WebDAV Base URI') }}
                        </template>
                    </b-wrapped-form-group>

                    <b-wrapped-form-group
                        id="form_edit_webDavUsername"
                        class="col-md-12 col-lg-6"
                        :field="form.webDavUsername"
                    >
                        <template #label>
                            {{ $gettext('WebDAV Username') }}
                        </template>
                    </b-wrapped-form-group>

                    <b-wrapped-form-group
                        id="form_edit_webDavPassword"
                        class="col-md-12 col-lg-6"
                        :field="form.webDavPassword"
                    >
                        <template #label>
                            {{ $gettext('WebDAV Password') }}
                        </template>
                    </b-wrapped-form-group>
                </div>
            </b-form-group>
        </b-card-body>
    </section>
</template>

<script setup>
import BWrappedFormGroup from "~/components/Form/BWrappedFormGroup.vue";

const props = defineProps({
    form: {
        type: Object,
        required: true
    }
});
</script>
