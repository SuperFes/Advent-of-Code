import {Divider, Paper, Typography} from "@mui/material";

export default function AoCDescription({title, text}) {
    return (
        <Paper sx={{p:1,my:2,mr:2}}>
            <Typography variant={'h3'}>{title}</Typography>
            <Typography>{text}</Typography>
        </Paper>
    )
}
