<template>
    <nav id="sidebar" >
        <div class="sidebar-content">
            <div class="sidebar-brand">
                <a href="#">Vuejs demo</a>
                <div id="close-sidebar" @click="closemenu">
                    <i class="fa fa-list"></i>
                </div>
            </div>
            <div class="sidebar-header">
                <div class="user-pic">
                    <img class="img-responsive img-rounded" src="https://raw.githubusercontent.com/azouaoui-med/pro-sidebar-template/gh-pages/src/img/user.jpg" alt="User picture">
                </div>
                <div class="user-info">
          <span class="user-name">admin
          </span>
                    <span class="user-role">Administrator</span>
                    <span class="user-status">
            <i class="fa fa-circle"></i>
            <span>Online</span>
          </span>
                </div>
            </div>
            <!-- sidebar-header  -->

            <!-- sidebar-search  -->
            <div class="sidebar-menu">
                <ul>
                    <li>
                        <router-link to="/example"><i class="fa fa-book"></i>
                            <span>example</span></router-link>
                    </li>
                </ul>
            </div>
            <!-- sidebar-menu  -->
        </div>
        <!-- sidebar-content  -->
    </nav>
</template>
<script>
    export default {
        methods:{
            closemenu(){
                $(".page-wrapper").removeClass("toggled");
            }
        },

    }
</script>