<?php
class Quotation_StatusCode extends SplEnum {

    // 未生成
    const   NOTGERERATE     = 1;

    // 生成中
    const   GENERATING      = 2;

    // 已生成
    const   GENERATED       = 3;

    // 生成失败
    const   GENERATEFAILED  = 4;
}