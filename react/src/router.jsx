import {createBrowserRouter, Navigate} from "react-router-dom";
import GuestLayout from "./components/GuestLayout";
import Login from "./views/Login";
import NotFound from "./views/NotFound";
import Signup from "./views/Signup";
import Index from "./views/Index.jsx";
import MainAppLayout from "./components/MainAppLayout.jsx";
import React from "react";

const router = createBrowserRouter([
  {
    path: '/',
    element: <MainAppLayout/>,
    children: [
      {
        path: '/',
        element: <Index/>,
      },
    ]
  },
  {
    path: '/',
    element: <GuestLayout/>,
    children: [
      {
        path: '/login',
        element: <Login/>
      },
      {
        path: '/signup',
        element: <Signup/>
      },
    ]
  },
  {
    path: "*",
    element: <NotFound/>
  }
])

export default router;
