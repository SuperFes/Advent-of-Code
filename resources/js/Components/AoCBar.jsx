import {AppBar, Box, Toolbar, Typography} from "@mui/material";

export default function AoCBar() {
    return (
        <Box sx={{ flexGrow: 1 }}>
            <AppBar position="static">
                <Toolbar>
                    <Typography variant={'h4'} sx={{fontFamily: '"Henny Penny", Itim, Helvetica, Arial, sans', color: '#1afa8a'}}>Advent of Code 2024</Typography>
                </Toolbar>
            </AppBar>
        </Box>
    );
}
