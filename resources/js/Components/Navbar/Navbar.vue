<template>
  <div>
    <header>
       <div class="flex-shrink-0 flex items-center">
        <a href="/dashboard">
          <jet-application-mark class="block h-9 w-auto" />
        </a>
          CSU Booking Platform
      </div>
    </header>

    <nav class="bg-white border-b border-gray-100 top-nav">
      <!-- Primary Navigation Menu -->
      <div>
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex ">
              <jet-nav-link
                href="/dashboard"
                :active="$page.currentRouteName == 'dashboard'"
              >
                Home
              </jet-nav-link>
              <jet-nav-link
                href="rooms"
                :active="$page.currentRouteName == 'rooms'"
              >
                Booking Management
              </jet-nav-link>
              <jet-nav-link
                href="users"
                :active="$page.currentRouteName == 'users'"
              >
                User Management
              </jet-nav-link>
              <jet-nav-link
                href="roles"
                :active="$page.currentRouteName == 'roles'"
              >
                MyAccount
              </jet-nav-link>
            </div>
          </div>

          <!-- Settings Dropdown -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="ml-3 relative">
              <jet-dropdown align="right" width="48">
                <template #trigger>
                  <button
                    v-if="$page.jetstream.managesProfilePhotos"
                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out"
                  >
                    <img
                      class="h-8 w-8 rounded-full object-cover"
                      :src="$page.user.profile_photo_url"
                      :alt="$page.user.name"
                    />
                  </button>

                  <button
                    v-else
                    class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out"
                  >
                    <div>{{ $page.user.name }}</div>

                    <div class="ml-1">
                      <svg
                        class="fill-current h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </button>
                </template>

                <template #content>
                  <!-- Account Management -->
                  <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Account
                  </div>

                  <jet-dropdown-link href="/user/profile">
                    Profile
                  </jet-dropdown-link>

                  <jet-dropdown-link
                    href="/user/api-tokens"
                    v-if="$page.jetstream.hasApiFeatures"
                  >
                    API Tokens
                  </jet-dropdown-link>

                  <div class="border-t border-gray-100"></div>

                  <!-- Team Management -->
                  <template v-if="$page.jetstream.hasTeamFeatures">
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Team
                    </div>

                    <!-- Team Settings -->
                    <jet-dropdown-link
                      :href="'/teams/' + $page.user.current_team.id"
                    >
                      Team Settings
                    </jet-dropdown-link>

                    <jet-dropdown-link
                      href="/teams/create"
                      v-if="$page.jetstream.canCreateTeams"
                    >
                      Create New Team
                    </jet-dropdown-link>

                    <div class="border-t border-gray-100"></div>

                    <!-- Team Switcher -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Switch Teams
                    </div>

                    <template v-for="team in $page.user.all_teams">
                      <form @submit.prevent="switchToTeam(team)">
                        <jet-dropdown-link as="button">
                          <div class="flex items-center">
                            <svg
                              v-if="team.id == $page.user.current_team_id"
                              class="mr-2 h-5 w-5 text-green-400"
                              fill="none"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                              ></path>
                            </svg>
                            <div>{{ team.name }}</div>
                          </div>
                        </jet-dropdown-link>
                      </form>
                    </template>

                    <div class="border-t border-gray-100"></div>
                  </template>

                  <!-- Authentication -->
                  <form @submit.prevent="logout">
                    <jet-dropdown-link as="button"> Logout </jet-dropdown-link>
                  </form>
                </template>
              </jet-dropdown>
            </div>
          </div>

          <!-- Hamburger -->
          <div class="-mr-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
            >
              <svg
                class="h-6 w-6"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 24 24"
              >
                <path
                  :class="{
                    hidden: showingNavigationDropdown,
                    'inline-flex': !showingNavigationDropdown,
                  }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{
                    hidden: !showingNavigationDropdown,
                    'inline-flex': showingNavigationDropdown,
                  }"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Responsive Navigation Menu -->
      <div
        :class="{
          block: showingNavigationDropdown,
          hidden: !showingNavigationDropdown,
        }"
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
              <jet-nav-link
                href="/dashboard"
                :active="$page.currentRouteName == 'dashboard'"
              >
                Home
              </jet-nav-link>
              <jet-nav-link
                href="rooms"
                :active="$page.currentRouteName == 'rooms'"
              >
                Booking Management
              </jet-nav-link>
              <jet-nav-link
                href="users"
                :active="$page.currentRouteName == 'users'"
              >
                User Management
              </jet-nav-link>
              <jet-nav-link
                href="roles"
                :active="$page.currentRouteName == 'roles'"
              >
                MyAccount
              </jet-nav-link>
          </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
          <div class="flex items-center px-4">
            <div class="flex-shrink-0">
              <img
                class="h-10 w-10 rounded-full"
                :src="$page.user.profile_photo_url"
                :alt="$page.user.name"
              />
            </div>

            <div class="ml-3">
              <div class="font-medium text-base text-gray-800">
                {{ $page.user.name }}
              </div>
              <div class="font-medium text-sm text-gray-500">
                {{ $page.user.email }}
              </div>
            </div>
          </div>

          <div class="mt-3 space-y-1">
            <jet-responsive-nav-link
              href="/user/profile"
              :active="$page.currentRouteName == 'profile.show'"
            >
              Profile
            </jet-responsive-nav-link>

            <jet-responsive-nav-link
              href="/user/api-tokens"
              :active="$page.currentRouteName == 'api-tokens.index'"
              v-if="$page.jetstream.hasApiFeatures"
            >
              API Tokens
            </jet-responsive-nav-link>

            <!-- Authentication -->
            <form method="POST" @submit.prevent="logout">
              <jet-responsive-nav-link as="button">
                Logout
              </jet-responsive-nav-link>
            </form>

            <!-- Team Management -->
            <template v-if="$page.jetstream.hasTeamFeatures">
              <div class="border-t border-gray-200"></div>

              <div class="block px-4 py-2 text-xs text-gray-400">
                Manage Team
              </div>

              <!-- Team Settings -->
              <jet-responsive-nav-link
                :href="'/teams/' + $page.user.current_team.id"
                :active="$page.currentRouteName == 'teams.show'"
              >
                Team Settings
              </jet-responsive-nav-link>

              <jet-responsive-nav-link
                href="/teams/create"
                :active="$page.currentRouteName == 'teams.create'"
              >
                Create New Team
              </jet-responsive-nav-link>

              <div class="border-t border-gray-200"></div>

              <!-- Team Switcher -->
              <div class="block px-4 py-2 text-xs text-gray-400">
                Switch Teams
              </div>

              <template v-for="team in $page.user.all_teams">
                <form @submit.prevent="switchToTeam(team)" :key="team.id">
                  <jet-responsive-nav-link as="button">
                    <div class="flex items-center">
                      <svg
                        v-if="team.id == $page.user.current_team_id"
                        class="mr-2 h-5 w-5 text-green-400"
                        fill="none"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        ></path>
                      </svg>
                      <div>{{ team.name }}</div>
                    </div>
                  </jet-responsive-nav-link>
                </form>
              </template>
            </template>
          </div>
        </div>
      </div>
    </nav>
  <nav class="sub-nav ">
    <Subnavbar v-if="$page.currentRouteName == 'dashboard'">
      <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
        <jet-nav-sub
          href="/dashboard"
          :active="$page.currentRouteName == 'dashboard'"
        >
          Dashboard
        </jet-nav-sub>
      </div>
    </Subnavbar>
    <Subnavbar v-else-if="$page.currentRouteName == 'rooms.index'">
      <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex ">
        <jet-nav-sub
          href="/rooms"
          :active="$page.currentRouteName == 'rooms.index'"
        >
          Rooms
        </jet-nav-sub>
      </div>
    </Subnavbar>
    <Subnavbar v-else-if="$page.currentRouteName == 'users.index'">
      <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex ">
        <jet-nav-sub
          href="/users"
          :active="$page.currentRouteName == 'users.index'"
        >
          Users
        </jet-nav-sub>
        <jet-nav-sub
          href="/roles"
          :active="$page.currentRouteName == 'roles.index'"
        >
          Roles
        </jet-nav-sub>
      </div>
    </Subnavbar>
    <Subnavbar v-else-if="$page.currentRouteName == 'roles.index'">
      <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
       <jet-nav-sub
          href="/users"
          :active="$page.currentRouteName == 'users.index'"
        >
          Users
        </jet-nav-sub>
        <jet-nav-sub
          href="/roles"
          :active="$page.currentRouteName == 'roles.index'"
        >
          Roles
        </jet-nav-sub>
      </div>
    </Subnavbar>
  </nav>
  </div>
</template>

<script>
import JetApplicationLogo from "@src/Jetstream/ApplicationLogo";
import JetApplicationMark from "@src/Jetstream/ApplicationMark";
import JetDropdown from "@src/Jetstream/Dropdown";
import JetDropdownLink from "@src/Jetstream/DropdownLink";
import JetNavLink from "@src/Jetstream/NavLink";
import JetResponsiveNavLink from "@src/Jetstream/ResponsiveNavLink";
import Subnavbar from "@src/Components/Navbar/Subnavbar";
import JetNavSub from "@src/Jetstream/SubNavLink";


export default {
  components: {
    JetApplicationLogo,
    JetApplicationMark,
    JetDropdown,
    JetDropdownLink,
    JetNavLink,
    JetResponsiveNavLink,
    Subnavbar,
    JetNavSub,
  },

  data() {
    return {
      showingNavigationDropdown: false,
    };
  },
  
  computed: {
    path() {
      return window.location.pathname;
    },
  },
};
</script>