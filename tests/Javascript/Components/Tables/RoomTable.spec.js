import {beforeEach, jest, test} from "@jest/globals";

jest.mock('laravel-jetstream')

import {createLocalVue, mount, shallowMount} from '@vue/test-utils'
import {InertiaApp} from '@inertiajs/inertia-vue'
import {InertiaForm} from 'laravel-jetstream'
import RoomTable from '@src/Components/Tables/RoomTable'
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

  const wrapper = shallowMount(RoomTable, {
    localVue,
    propsData: {
        rooms: [{
            id: 1,
            name: "name",
            building: "building",
            number: "1",
            floor: 1,
            status: "available",
            room_type: "Mezzanine"
        }]
    }
  })



  expect(wrapper.text()).toBeDefined()
})


test('should filter properly', () => {

  const wrapper = shallowMount(RoomTable, {
    localVue,
    propsData: {
        rooms: [{
            id: 1,
            name: "name",
            building: "building",
            number: "1",
            floor: 1,
            status: "available",
            room_type: "Mezzanine"
        }]
    }
  })

  wrapper.setData({ filter: '' })
  expect(wrapper.html()).toContain('<td class="text-center">1</td>')

  wrapper.setData({ filter: 'building' })
  expect(wrapper.vm.filter).toBe('building')
  expect(wrapper.html()).toContain('<td class="text-center">1</td>')

  wrapper.setData({ filter: 'thisfiltershouldnotwork' })
  expect(wrapper.vm.filter).toBe('thisfiltershouldnotwork')
  expect(wrapper.vm.filteredRooms.length).toBe(0)



  expect(wrapper.text()).toBeDefined()
})


test('should show advanced filters popup', () => {
    const wrapper = shallowMount(RoomTable, {
        localVue,
        propsData: {
            rooms: [{
                id: 1,
                name: "name",
                building: "building",
                number: "1",
                floor: 1,
                status: "available",
                room_type: "Mezzanine"
            }]
        }
    });
    wrapper.vm.advancedFilters();
    expect(wrapper.vm.showFilterModal).toBe(true);
})

test('should compute only active jsonfilter fields to send in post request', () => {
    const wrapper = shallowMount(RoomTable, {
        localVue,
        propsData: {
            rooms: [{
                id: 1,
                name: "name",
                building: "building",
                number: "1",
                floor: 1,
                status: "available",
                room_type: "Mezzanine"
            }]
        }
    });

    wrapper.vm.jsonFilters.food = true;
    expect(wrapper.vm.activeJsonFilters).toStrictEqual({"food": true, "recurrences": []});
})

test('should change missingDates when date is added or removed', () => {
  const wrapper = shallowMount(RoomTable, {
    localVue,
    propsData: {
      rooms: [{
        id: 1,
        name: "name",
        building: "building",
        number: "1",
        floor: 1,
        status: "available",
        room_type: "Mezzanine"
      }]
    }
  });

  wrapper.vm.addDate();
  expect(wrapper.vm.missingDates).toBe(true);

  wrapper.vm.removeDate(wrapper.vm.jsonFilters.recurrences[0]);
  wrapper.vm.jsonFilters.recurrences.push({
    "start_time": "Now",
    "end_time": "tomorrow"
  });

  expect(wrapper.vm.missingDates).toBe(false);
})


