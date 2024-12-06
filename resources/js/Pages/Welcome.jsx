import React, {Suspense, useDeferredValue} from "react";
import {Head}                              from '@inertiajs/react';
import {
    Box,
    Tabs, Tab,
    Typography, Container, LinearProgress
}                                          from "@mui/material";
import Grid                                from '@mui/material/Grid2';
import AoCBar                                                                   from "@/Components/AoCBar.jsx";
import {StarBorderOutlined, StarHalf, StarHalfTwoTone, StarOutlined, StarSharp} from "@mui/icons-material";

const Days = [
    {label: '01', content: React.lazy(() => import("@/Days/01/Problem.jsx")), enabled: true, stars: 2},
    {label: '02', content: React.lazy(() => import("@/Days/02/Problem.jsx")), enabled: false, stars: 0},
    {label: '03', content: React.lazy(() => import("@/Days/03/Problem.jsx")), enabled: false, stars: 0},
    {label: '04', content: React.lazy(() => import("@/Days/04/Problem.jsx")), enabled: false, stars: 0},
    {label: '05', content: React.lazy(() => import("@/Days/05/Problem.jsx")), enabled: false, stars: 0},
    {label: '06', content: React.lazy(() => import("@/Days/06/Problem.jsx")), enabled: false, stars: 0},
    {label: '07', content: React.lazy(() => import("@/Days/07/Problem.jsx")), enabled: false, stars: 0},
    {label: '08', content: React.lazy(() => import("@/Days/08/Problem.jsx")), enabled: false, stars: 0},
    {label: '09', content: React.lazy(() => import("@/Days/09/Problem.jsx")), enabled: false, stars: 0},
    {label: '10', content: React.lazy(() => import("@/Days/10/Problem.jsx")), enabled: false, stars: 0},
    {label: '11', content: React.lazy(() => import("@/Days/11/Problem.jsx")), enabled: false, stars: 0},
    {label: '12', content: React.lazy(() => import("@/Days/12/Problem.jsx")), enabled: false, stars: 0},
    {label: '13', content: React.lazy(() => import("@/Days/13/Problem.jsx")), enabled: false, stars: 0},
    {label: '14', content: React.lazy(() => import("@/Days/14/Problem.jsx")), enabled: false, stars: 0},
    {label: '15', content: React.lazy(() => import("@/Days/15/Problem.jsx")), enabled: false, stars: 0},
    {label: '16', content: React.lazy(() => import("@/Days/16/Problem.jsx")), enabled: false, stars: 0},
    {label: '17', content: React.lazy(() => import("@/Days/17/Problem.jsx")), enabled: false, stars: 0},
    {label: '18', content: React.lazy(() => import("@/Days/18/Problem.jsx")), enabled: false, stars: 0},
    {label: '19', content: React.lazy(() => import("@/Days/19/Problem.jsx")), enabled: false, stars: 0},
    {label: '20', content: React.lazy(() => import("@/Days/20/Problem.jsx")), enabled: false, stars: 0},
    {label: '21', content: React.lazy(() => import("@/Days/21/Problem.jsx")), enabled: false, stars: 0},
    {label: '22', content: React.lazy(() => import("@/Days/22/Problem.jsx")), enabled: false, stars: 0},
    {label: '23', content: React.lazy(() => import("@/Days/23/Problem.jsx")), enabled: false, stars: 0},
    {label: '24', content: React.lazy(() => import("@/Days/24/Problem.jsx")), enabled: false, stars: 0},
    {label: '25', content: React.lazy(() => import("@/Days/25/Problem.jsx")), enabled: false, stars: 0},
];

const Stars = [
    <StarBorderOutlined/>,
    <StarHalfTwoTone/>,
    <StarOutlined/>
];

export default function Welcome({auth, laravelVersion, phpVersion}) {
    const [value, setValue]     = React.useState(0);
    const [preLoad, setPreLoad] = React.useState(<Container id={"#Preloader"} sx={{
        p        : 2,
        width    : 100,
        height   : 100,
        textAlign: 'center'
    }}>
        <Typography>&nbsp;Loaderating...&nbsp;</Typography>
        <LinearProgress sx={{width: 100}} />
    </Container>);
    const PreLoader             = React.useDeferredValue(preLoad);

    const Contents             = Days[value].content;

    const handleChange = (event, newValue) => {
        console.log(newValue);
        setValue(newValue);
    };

    return (
        <>
            <AoCBar />
            <Head title="Enjoy" />
            <Box sx={{flexGrow: 1}}>
                <Grid container spacing={2}>
                    <Grid size={"auto"}>
                        <Box sx={{borderRight: 1, borderColor: 'divider'}}>
                            <div><Typography>Days</Typography></div>
                            <Tabs orientation="vertical" variant="scrollable" value={value} onChange={handleChange} aria-label="Vertical tabs example" sx={{
                                borderRight: 1,
                                borderColor: 'divider'
                            }}>
                                {[...Array(25)].map((x, i) =>
                                    <Tab
                                        label={Days[i].label}
                                        icon={Stars[Days[i].stars]}
                                        iconPosition="end"
                                        disabled={ Days[i].enabled !== true }
                                    />
                                )}
                            </Tabs>
                        </Box>
                    </Grid>
                    <Grid size={"grow"}>
                        <Box component="section">
                            <div id={"#DaysContents"}>
                                <Suspense fallback={PreLoader}>
                                    <Contents/>
                                </Suspense>
                            </div>
                        </Box>
                    </Grid>
                </Grid>
            </Box>
        </>
    );
}
