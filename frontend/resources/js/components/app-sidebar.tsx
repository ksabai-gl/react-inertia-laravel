'use client';

import {
    Book,
    BookCheck,
    BookHeart,
    BookImage,
    LayoutDashboard,
    PhoneCall,
    Radar,
    Server,
    TestTube2,
} from 'lucide-react';
import * as React from 'react';

import { NavMain } from '@/components/nav-main';
import { NavSecondary } from '@/components/nav-secondary';
import { ProjectSwitcher } from '@/components/project-switcher';
import {
    Sidebar,
    SidebarContent,
    SidebarHeader,
} from '@/components/ui/sidebar';

const data = {
    projects: [
        {
            logo: PhoneCall,
            title: 'IVR Testing Platform',
            subtitle: 'Regression · Discovery',
        },
    ],
    navMain: [
        {
            title: 'Dashboard',
            url: '/',
            icon: LayoutDashboard,
        },
        {
            title: 'IVR Platform',
            url: '/',
            icon: Server,
        },
        {
            title: 'Regression Tests',
            url: '/',
            icon: TestTube2,
        },
        {
            title: 'Discovery Scans',
            url: '/',
            icon: Radar,
        },
    ],
    navSecondary: [
        {
            title: 'Readme',
            url: 'https://github.com/ferjal0/react-inertia-laravel/blob/main/README.md',
            icon: Book,
        },
        {
            title: 'Getting Started',
            url: 'https://github.com/ferjal0/react-inertia-laravel/blob/main/docs/getting-started.md',
            icon: BookHeart,
        },
        {
            title: 'Frontend Docs',
            url: 'https://github.com/ferjal0/react-inertia-laravel/blob/main/docs/frontend.md',
            icon: BookImage,
        },
        {
            title: 'Backend Docs',
            url: 'https://github.com/ferjal0/react-inertia-laravel/blob/main/docs/backend.md',
            icon: BookCheck,
        },
    ],
};

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
    return (
        <Sidebar variant="inset" collapsible="icon" {...props}>
            <SidebarHeader>
                <ProjectSwitcher projects={data.projects} />
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={data.navMain} />
                <NavSecondary items={data.navSecondary} className="mt-auto" />
            </SidebarContent>
        </Sidebar>
    );
}
