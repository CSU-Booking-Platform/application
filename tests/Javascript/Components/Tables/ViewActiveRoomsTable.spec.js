import {beforeEach, jest, test} from "@jest/globals";

jest.mock('laravel-jetstream')

import {createLocalVue, mount, shallowMount} from '@vue/test-utils'
import {InertiaApp} from '@inertiajs/inertia-vue'
import {InertiaForm} from 'laravel-jetstream'
import ViewActiveRoomsTable from '@src/Components/Tables/ViewActiveRoomsTable'
import {InertiaFormMock} from "@test/__mocks__/laravel-jetstream";
import moment from "moment";

let localVue;

beforeEach(() => {
  InertiaFormMock.error.mockClear()
  InertiaFormMock.post.mockClear()

  localVue = createLocalVue()
  localVue.use(InertiaApp)
  localVue.use(InertiaForm)

});
afterEach(() => {
  localVue = null
})


test('should mount without crashing', () => {

  const wrapper = shallowMount(ViewActiveRoomsTable, {
    localVue,
    propsData: {
        rooms: [{
            id: 1,
            name: "name",
            building: "building",
            number: "1",
            floor: 1,
            status: "available"
        }],
        
        availableRoomTypes: ['test']
    }
  })

  

  expect(wrapper.text()).toBeDefined()
})

test('deleteRoom()', () => {

    let mockRoomBeingDeleted = {
        id: 10
    }

    InertiaFormMock.delete.mockReturnValueOnce({
        then(callback) {
            callback({})
        }
    })

    const wrapper = shallowMount(ViewActiveRoomsTable, {
        localVue,
        propsData: {
            rooms: [{
                id: 1,
                name: "name",
                building: "building",
                number: "1",
                floor: 1,
                status: "available"
            }],
            
            availableRoomTypes: ['test']
        },
        data() {
            return {
                roomBeingDeleted: mockRoomBeingDeleted
            }
        }
    })

    wrapper.vm.deleteRoom()

    expect(InertiaFormMock.delete).toBeCalledWith('/rooms/' + mockRoomBeingDeleted.id, {
        preserveScroll: true,
        preserveState: true,
    })

    expect(wrapper.vm.$data.roomBeingDeleted).toBe(null)
})

test('openUpdateRestrictionsModal', () => {
    let room = {
        restrictions: ["role1", "role2"]
    }
    const wrapper = shallowMount(ViewActiveRoomsTable, {
        localVue,
        propsData: {
            rooms: [{
                id: 1,
                name: "name",
                building: "building",
                number: "1",
                floor: 1,
                status: "available"
            }],
            
            availableRoomTypes: ['test']
        }
    })
    wrapper.vm.openEditModal(room)
    expect(wrapper.vm.$data.roomRestBeingUpdated).toBe(room);
})

test('updateRoomRestrictions()', () => {

    let mockRoomResBeingUpdated = {
        id: 10,
        roles:['role1', 'roles2']
    }

    InertiaFormMock.put.mockReturnValueOnce({
        then(callback) {
            callback({})
        }
    })

    const wrapper = shallowMount(ViewActiveRoomsTable, {
        localVue,
        propsData: {
            rooms: [{
                id: 1,
                name: "name",
                building: "building",
                number: "1",
                floor: 1,
                status: "available"
            }],
            
            availableRoomTypes: ['test']
        },
        data() {
            return {
                roomRestBeingUpdated: mockRoomResBeingUpdated
            }
        }
    })

    wrapper.vm.updateRestrictions()

    expect(InertiaFormMock.put).toBeCalledWith('/room/restrictions/'  + mockRoomResBeingUpdated.id, {
        preserveScroll: true,
        preserveState: true,
    })

    expect(wrapper.vm.$data.roomRestBeingUpdated).toBe(null)
})

test('should filter properly', () => {

  const wrapper = shallowMount(ViewActiveRoomsTable, {
    localVue,
    propsData: {
        rooms: [{
            id: 1,
            name: "name",
            building: "building",
            number: "1",
            floor: 1,
            status: "available"
        }],
        availableRoomTypes: ['test']
    }
  })

  wrapper.setData({ filter: '' })
  expect(wrapper.html()).toContain('<td class="text-center lt-grey p-3">1</td>')
  
    
  wrapper.setData({ filter: 'building' })
  expect(wrapper.vm.filter).toBe('building')
  expect(wrapper.html()).toContain('<td class="text-center lt-grey p-3">1</td>')

  wrapper.setData({ filter: 'thisfiltershouldnotwork' })
  expect(wrapper.vm.filter).toBe('thisfiltershouldnotwork')
  expect(wrapper.vm.filteredRooms.length).toBe(0)

  

  expect(wrapper.text()).toBeDefined()
})