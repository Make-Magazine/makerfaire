<!--template file-->
<template>
    content block pulled in
</template>
<!--
<template>    
    <b-row  v-for="(block,block_id) in tab.tab_content.expand.blocks" :key="block_id" >                                                    
                    
        <b-col v-for="(fields, column_id) in block.columns" :key="'dyn-column-' + column_id">                                                   
                    
            <div v-for="(field, field_id) in fields" :key="maker_id-'field-' + field_id">
                
                <div v-if="field.value">
                    <label>{{ field.label }}</label>
                    <div v-if="field.type === 'fileupload'">
                        <b-img thumbnail fluid :src="field.value"
                            :alt="field.label"></b-img>
                    </div>
                    <span v-else-if="field.type === 'multipleFiles'">
                        <b-container class="p-4 bg-dark">
                            <b-row>
                                <b-col fluid="sm" :key="maker_id+'-img-' + image_id"
                                    v-for="(image,image_id) in field.value">
                                    <b-img thumbnail fluid :src="image" :alt="field.label"
                                        class="multiImage"></b-img>
                                </b-col>
                            </b-row>
                        </b-container>
                    </span>
                    <span v-else-if="field.type === 'website'">
                        <b-link :href="field.value" target="_blank">{{field.value}}</b-link>
                    </span>
                    <span v-else-if="field.type === 'video'">
                        <b-embed type="iframe" aspect="16by9" :src="field.value"
                            allowfullscreen></b-embed>
                    </span>
                    <span v-else-if="field.type === 'notes'">
                        <b-list-group>
                            <b-list-group-item v-for="(note,i) in field.value"
                                :key="maker_id+'-note-' + i">
                                <b-row>
                                    <b-col>{{note.date_created}}</b-col>
                                    <b-col>{{note.user_name}}</b-col>
                                    <b-col cols="8">{{note.value}}</b-col>
                                </b-row>
                            </b-list-group-item>
                        </b-list-group>
                    </span>
                    <span v-else-if="field.type === 'checkbox'">
                        <b-list-group>
                            <b-list-group-item v-for="(value,i) in field.value"
                                :key="maker_id+'-list-' + i">
                                {{value}}
                            </b-list-group-item>
                        </b-list-group>
                    </span>
                    <span v-else-if="field.type === 'listRepeat'">
                        <div v-for="(value,i) in field.value">
                            <b-table striped hover :items="value"></b-table>
                        </div>

                    </span>
                    <span v-else-if="field.type === 'list'">
                        <b-table striped hover :items="field.value"></b-table>
                    </span>
                    <span v-else-if="field.type === 'address'">
                        <b-list-group>
                            <b-list-group-item v-for="(value,i) in field.value"
                                :key="maker_id+'-address-' + i">
                                {{value.label}} - {{value.value}}
                            </b-list-group-item>
                        </b-list-group>
                    </span>
                    <span v-else-if="field.type === 'html'" v-html="field.value"></span>
                    <span v-else>
                        {{field.value}}
                    </span>
                </div>
            </div>
        </b-col>
    </b-row>
</template>    
-->