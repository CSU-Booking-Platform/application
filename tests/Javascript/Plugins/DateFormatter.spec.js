import {beforeEach, jest, test} from "@jest/globals";

jest.mock('laravel-jetstream')

import {createLocalVue, shallowMount} from '@vue/test-utils'
import {InertiaApp} from '@inertiajs/inertia-vue'
import {InertiaForm} from 'laravel-jetstream'
import DateFormatter from "@src/Plugins/date-formatter";
import {InertiaFormMock} from "@test/__mocks__/laravel-jetstream";
import moment from "moment";

let localVue

beforeEach(() => {
  InertiaFormMock.error.mockClear()
  InertiaFormMock.post.mockClear()
  InertiaFormMock.delete.mockClear()

  localVue = createLocalVue()
  localVue.use(InertiaApp)
  localVue.use(InertiaForm)
  localVue.use(DateFormatter)

});

test('calendar should return proper calendar date', () => {
  const date = localVue.prototype.$calendar("2021-02-08T18:03:00.000000Z");
  expect(date).toBe("February 8, 2021 1:03 PM");
})


test('fromNow should return proper date from current timestamp', () => {
  const date = localVue.prototype.$fromNow("2021-02-08T18:03:00.000000Z");
  const expected = moment("2021-02-08T18:03:00.000000Z").local().fromNow();
  expect(date).toBe(expected);
})
