<template>
  <app-layout>
    <div id="calendar_res">
      <h1>Booked Rooms Calendar</h1>
      <calendar-view
        :show-date="showDate"
        :items="items"
        :currentPeriodLabel="currentPeriodLabel"
        @click-item = "onClickItem"
        class="theme-default holiday-us-traditional holiday-us-official">
        <template #header="{ headerProps }">
          <calendar-view-header
            :header-props="headerProps"

            @input="setShowDate"/>
        </template>
      </calendar-view>
    </div>
  </app-layout>
</template>

<script>
import {CalendarView, CalendarViewHeader} from "vue-simple-calendar"
import AppLayout from "@src/Layouts/AppLayout";
import JetSectionBorder from "@src/Jetstream/SectionBorder";

import "vue-simple-calendar/static/css/default.css"
import "vue-simple-calendar/static/css/holidays-us.css"

var currentDate = new Date();
var currentMonth = currentDate.getMonth();
var currentDay = currentDate.getDate();
currentMonth = currentMonth + 1;
if (currentMonth < 10) {
  currentMonth = '0' + currentMonth
}
if (currentDay < 10) {
  currentDay = '0' + currentDay
}
export default {
  data: function () {
    return {
      showDate: new Date(),
      items: [],
      currentPeriodLabel: "Today is " + currentDay + "-" + currentMonth
    }
  },
  props: {
    reservations: {
      type: Array,
      default: function () {
        return []
      },
    },
    canViewBookings: Boolean
  },
  components: {
    AppLayout,
    JetSectionBorder,
    CalendarView,
    CalendarViewHeader,
  },
  methods: {
    setShowDate(d) {
      this.showDate = d;
    },
    onClickItem(e) {
      //console.log(e)
      //alert(`you clicked ${e.originalItem.url}!`)
      if(canViewBookings){
        window.open(e.originalItem.url, '_blank').focus();
      }
      //this.message = `You clicked: ${e.title}`
    },
  },
  mounted() {
    this.items = this.reservations ?? [];
  }
}
</script>

<style>
#calendar_res {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  color: #2c3e50;
  height: 67vh;
  width: 90vw;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 7%;
}
</style>



