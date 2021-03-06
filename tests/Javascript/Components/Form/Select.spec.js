import {beforeEach, expect, jest, test} from "@jest/globals";

jest.mock('laravel-jetstream')

import {createLocalVue, mount, shallowMount} from '@vue/test-utils'
import {InertiaApp} from '@inertiajs/inertia-vue'
import {InertiaForm} from 'laravel-jetstream'
import Component from '@src/Components/Form/Select'

let localVue

beforeEach(() => {
    localVue = createLocalVue()
    localVue.use(InertiaApp)
    localVue.use(InertiaForm)

});

test('should mount without crashing', () => {
    const wrapper = mount(Component, {localVue})

    wrapper.vm.focus();

    expect(wrapper.text()).toBeDefined();
})


test('verify computed proxy field', () => {
    const wrapper = shallowMount(Component, {localVue})

    wrapper.setData({ selected: true });

    expect(wrapper.vm.selected).toBe(true);

})
